<template>
  <div>
    <h1>Upload File Datax</h1>
    <input type="file" @change="handleFileUpload" name="file" />
    <button @click="submitFile">Upload</button>
  </div>
</template>

<script>
import { ref } from 'vue';
import axios from 'axios';

export default {
  setup() {
    const file = ref(null);

    function handleFileUpload(event) {
      file.value = event.target.files[0];
    }

    async function submitFile() {
      if (file.value) {
        await uploadFile(file.value);
      } else {
        console.error('No file selected');
      }
    }

    async function uploadFile(file) {
      const formData = new FormData();
      formData.append('file', file);

      try {
        let response = await axios.post('/penetapan/import', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });

        console.log('File uploaded successfully:', response.data);
      } catch (error) {
        console.error('Error uploading file:', error);
      }
    }

    return {
      handleFileUpload,
      submitFile
    };
  }
};
</script>
