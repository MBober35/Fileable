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
              <span>{{ error }}</span>
              <br>
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
  </div>
</template>

<script>
export default {
  props: {
    uploadUrl: {
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
    }
  },

  methods: {
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

    uploadFiles() {
      this.resetMessages();
      this.uploadSingleFile();
    },

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
            console.log(result);
            if (result.success) {
              this.fileContents.shift();
              if (this.fileContents.length) {
                this.uploadSingleFile();
              }
            }
            else {
              Swal.fire({
                icon: 'error',
                title: 'Упс...',
                text: 'Что-то пошло не так!',
                footer: result.message,
              })
            }
          })
          .catch(error => {
            let data = error.response.data;
            if (data.hasOwnProperty("errors")) {
              this.errors = data.errors;
            }
            else {
              Swal.fire({
                icon: 'error',
                title: 'Упс...',
                text: 'Что-то пошло не так!',
                footer: data.message,
              })
            }
          })
          .finally(() => {
            this.loading = false;
          })
    }
  }
}
</script>

<style scoped>

</style>
