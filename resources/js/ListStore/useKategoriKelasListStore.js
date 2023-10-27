import axios from '@axios'
import { defineStore } from 'pinia'

export const useKategoriKelasListStore = defineStore('KategoriKelasListStore', {
  actions: {
    // 👉 Fetch kategoriKelass data
    fetchKategoriKelas(params) { return axios.get('/api/kelas/kategori', { params }) },
    
    // 👉 Fetch kategoriKelass data all
    fetchKategoriKelasAll(params) { return axios.get('/api/kelas/kategori/all', { params }) },

    // 👉 Add KategoriKelas
    addKategoriKelas(kategoriKelasData) {
      return new Promise((resolve, reject) => {
        axios({
          url: '/api/kelas/kategori',
          method: 'POST',
          data: kategoriKelasData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })

          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single kategoriKelas
    fetchSingleKategoriKelas(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/api/kelas/kategori/${id}`).then(response => resolve(response.data.kategori_kelas)).catch(error => reject(error))
      })
    },

    // 👉 Update KategoriKelas
    updateKategoriKelas(kategoriKelasData) {
      return new Promise((resolve, reject) => {
        axios({
          url: `/api/kelas/kategori/${kategoriKelasData.get('id')}`,
          method: 'POST',
          data: kategoriKelasData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Delete KategoriKelas
    deleteKategoriKelas(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/api/kelas/kategori/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
