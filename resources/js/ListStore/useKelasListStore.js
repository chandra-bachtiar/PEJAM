import axios from '@axios'
import { defineStore } from 'pinia'

export const useKelasListStore = defineStore('KelasListStore', {
  actions: {
    // 👉 Fetch Kelass data
    fetchKelas(params) { return axios.get('/api/kelas', { params }) },

    // 👉 Add Kelas
    addKelas(KelasData) {
      return new Promise((resolve, reject) => {
        axios({
          url: '/api/kelas',
          method: 'POST',
          data: KelasData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })

          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single Kelas
    fetchSingleKelas(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/api/kelas/${id}`).then(response => resolve(response.data.kelas)).catch(error => reject(error))
      })
    },

    // 👉 Update Kelas
    updateKelas(KelasData) {
      return new Promise((resolve, reject) => {
        axios({
          url: `/api/kelas/${KelasData.get('id')}`,
          method: 'POST',
          data: KelasData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Delete Kelas
    deleteKelas(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/api/kelas/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
