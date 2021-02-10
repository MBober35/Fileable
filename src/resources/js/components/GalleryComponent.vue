<template>
    <div class="row">
        <div class="col-12">
            <form>
                <div class="mb-3">
                    <label for="galleryFileMultiple"
                           class="form-label">
                        Загрузка файлов
                    </label>
                    <div class="input-group">
                        <input class="form-control"
                               type="file"
                               @change.prevent="getImage"
                               id="galleryFileMultiple"
                               multiple>
                        <button type="button"
                                @click="uploadFiles"
                                :disabled="loading || ! fileContents.length"
                                class="btn btn-outline-secondary">
                            <span class="spinner-border spinner-border-sm"
                                  v-if="loading"
                                  role="status"
                                  aria-hidden="true">
                            </span>
                            Загрузить
                        </button>
                    </div>
                </div>
                <div class="alert alert-danger" role="alert" v-if="Object.keys(errors).length">
                    <template v-for="field in errors">
                        <template v-for="error in field">
                            <div class="alert-message">{{ error }}</div>
                        </template>
                    </template>
                </div>
            </form>
        </div>
        <div class="col-6 col-md-2 mb-2" v-for="(item, name) in fileContents">
            <div class="input-group mb-3">
                <input type="text"
                       class="form-control"
                       placeholder="Имя файла"
                       v-model="item.name"
                       aria-label="Имя файла">
                <button class="btn btn-outline-danger"
                        :disabled="loading"
                        @click="fileContents.splice(name, 1)"
                        type="button">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>

            <img :src="item.content" alt="Предпросмотр" class="rounded m-auto d-block img-fluid">
        </div>
        <div class="col-12">
            <div class="table-responsive position-relative">
                <div class="button-cover" v-if="priorityChange">
                    <button class="btn btn-success mb-3"
                            :disabled="loading"
                            @click="changeOrder">
                            <span class="spinner-border spinner-border-sm"
                                  v-if="loading"
                                  role="status"
                                  aria-hidden="true">
                            </span>
                        Сохранить порядок
                    </button>
                </div>
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Изображение</th>
                        <th>Имя</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <draggable :list="images" group="images" tag="tbody" handle=".handle" @change="checkMove">
                        <tr v-for="image in images" :key="image.id">
                            <td>
                                <i class="fa fa-align-justify handle"></i>
                            </td>
                            <td>
                                <img :src="image.src" :alt="image.name">
                            </td>
                            <td>
                                {{ image.name }}
                            </td>
                            <td>
                                <div role="toolbar" class="btn-toolbar">
                                    <div class="btn-group mr-1">
                                        <button type="button"
                                                @click="showEditModal(image)"
                                                :disabled="loading"
                                                class="btn btn-primary">
                                            <i class="far fa-edit"></i>
                                        </button>
                                        <button type="button"
                                                @click="destroy(image)"
                                                :disabled="loading"
                                                class="btn btn-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    </draggable>
                </table>
            </div>
        </div>
        <div class="modal" id="editModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Название изображения</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" v-if="chosenImage">
                        <div class="mb-3">
                            <label for="image-name" class="visually-hidden">Email address</label>
                            <input type="email"
                                   class="form-control"
                                   id="image-name"
                                   :disabled="loading"
                                   v-model="chosenImage.nameChanged"
                                   placeholder="Название">
                        </div>
                    </div>
                    <div class="modal-footer" v-if="chosenImage">
                        <button type="button"
                                class="btn btn-secondary"
                                :disabled="loading"
                                @click="closeModal()">
                            <span class="spinner-border spinner-border-sm"
                                  v-if="loading"
                                  role="status"
                                  aria-hidden="true">
                            </span>
                            Отмена
                        </button>
                        <button type="button"
                                :disabled="loading || ! chosenImage.nameChanged.length"
                                @click="changeName"
                                class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm"
                                  v-if="loading"
                                  role="status"
                                  aria-hidden="true">
                            </span>
                            Обновить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable';

export default {
    components: {
        draggable,
    },

    props: {
        uploadUrl: {
            type: String,
            required: true
        },
        getUrl: {
            type: String,
            required: true
        }
    },

    data() {
        return {
            messages: [],
            errors: {},
            fileContents: [],
            loading: false,
            images: [],
            priorityChange: false,
            chosenImage: false,
            modal: false,
        }
    },

    created() {
        this.getList();
    },

    mounted() {
        this.modal = new bootstrap.Modal(document.getElementById("editModal"));
    },

    computed: {
        orderData() {
            let ids = [];
            for (let item in this.images) {
                if (this.images.hasOwnProperty(item)) {
                    ids.push(this.images[item].id);
                }
            }
            return ids;
        }
    },

    methods: {
        // Удалить изображение.
        destroy(image) {
            Swal.fire({
                title: "Вы уверены?",
                text: "Изображение будет невозможно досстановить!",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Отмена",
                confirmButtonText: "Да, удалить!",
            }).then((result) => {
                if (result.value) {
                    this.loading = true;
                    this.resetMessages();
                    axios
                        .delete(image.destroyUrl)
                        .then(response => {
                            let result = response.data;
                            if (result.success) {
                                this.images = result.images;
                            } else {
                                this.fireError(result.mesage);
                            }
                        })
                        .catch(error => {
                            this.parseErrors(error.response.data);
                        })
                        .finally(() => {
                            this.loading = false;
                        })
                }
            })
        },
        // Обновить название.
        changeName() {
            this.loading = true;
            this.resetMessages();
            axios
                .put(this.chosenImage.updateUrl, {
                    "name": this.chosenImage.nameChanged,
                })
                .then(response => {
                    let result = response.data;
                    if (result.success) {
                        this.images = result.images;
                    } else {
                        this.fireError(result.message);
                    }
                })
                .catch(error => {
                    this.parseErrors(error.response.data);
                })
                .finally(() => {
                    this.loading = false;
                    this.modal.hide();
                })
        },
        // Закрыть форму.
        closeModal() {
            this.chosenImage.nameChanged = this.chosenImage.name;
            this.modal.hide();
        },
        // Открыть форму.
        showEditModal(image) {
            this.chosenImage = image;
            this.modal.show();
        },
        // Изменить порядок вывода.
        changeOrder() {
            this.loading = true;
            this.resetMessages();
            axios
                .put(this.uploadUrl, {
                    images: this.orderData
                })
                .then(response => {
                    let result = response.data;
                    if (result.success) {
                        this.images = result.images;
                        this.priorityChange = false;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Упс...',
                            text: 'Что-то пошло не так!',
                            footer: result.message,
                        })
                    }
                })
                .catch(error => {
                    this.parseErrors(error.response.data);
                })
                .finally(() => {
                    this.loading = false;
                })
        },
        // Приоритет изменен.
        checkMove() {
            this.priorityChange = true;
        },
        // Получить список изображений.
        getList() {
            this.loading = true;
            axios
                .get(this.getUrl)
                .then(response => {
                    let result = response.data;
                    if (result.success) {
                        this.images = result.images;
                    } else {
                        this.fireError(result.message);
                    }
                })
                .catch(error => {
                    this.parseErrors(error.response.data);
                })
                .finally(() => {
                    this.loading = false;
                })
        },
        // Очистить сообщения.
        resetMessages() {
            this.messages = [];
            this.errors = {};
        },
        // Получить выбранное изображение.
        getImage(event) {
            this.resetMessages();
            this.fileContents = [];
            for (let item in event.target.files) {
                if (event.target.files.hasOwnProperty(item)) {
                    this.selectImage(event.target.files[item]);
                }
            }
        },
        // Сформировать данные по изображению.
        selectImage(file) {
            let reader = new FileReader();
            reader.onload = (function (inputFile, contents) {
                return function (event) {
                    let content = event.target.result;
                    let originalName = inputFile.name;
                    let exploded = originalName.split(".");
                    let name = originalName;
                    if (exploded.length > 1) name = exploded[0];
                    contents.push({
                        file: inputFile,
                        name: name,
                        content: content
                    })
                }
            })(file, this.fileContents);
            reader.readAsDataURL(file);
        },
        // Начать загрузку файлов.
        uploadFiles() {
            this.resetMessages();
            this.uploadSingleFile();
        },
        // Отправить файл.
        uploadSingleFile() {
            this.loading = true;
            let formData = new FormData();
            let file = this.fileContents[0].file;
            let name = this.fileContents[0].name;
            formData.append("image", file);
            formData.append("name", name);
            axios
                .post(this.uploadUrl, formData, {
                    responseType: "json"
                })
                .then(response => {
                    let result = response.data;
                    if (result.success) {
                        this.fileContents.shift();
                        this.images = result.images;
                        if (this.fileContents.length) {
                            this.uploadSingleFile();
                        }
                    } else {
                        this.fireError(result.message);
                    }
                })
                .catch(error => {
                    this.parseErrors(error.response.data);
                })
                .finally(() => {
                    this.loading = false;
                })
        },
        // Обработать ошибки валидации.
        parseErrors(result) {
            if (result.hasOwnProperty("errors")) {
                this.errors = result.errors;
            } else {
                this.fireError(result.message);
            }
        },
        // Вызвать ошибку.
        fireError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Упс...',
                text: 'Что-то пошло не так!',
                footer: message,
            })
        }
    }
}
</script>

<style scoped>
.handle {
    cursor: move;
}

.button-cover {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    display: flex;
    width: 100%;
    align-items: center;
    justify-content: center;
}
</style>
