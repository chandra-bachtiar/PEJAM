import axios from '@axios'
import { defineStore } from 'pinia'

export const useMateriListStore = defineStore('MateriListStore', {
  actions: {
    // 👉 Fetch Materi data
    fetchMateri(id) { return axios.get(`/api/kelas/materi/${id}`) },

    // 👉 fetch materi by kelas_id and materi_id
    fetchMateriById(kelas_id, materi_id) { return axios.get(`/api/kelas/materi/show/${materi_id}`) },

    // 👉 Add Materi
    addMateri(materiData) {
      return new Promise((resolve, reject) => {
        axios({
          url: '/api/kelas/materi',
          method: 'POST',
          data: materiData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })

          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Update Materi
    updateMateri(materiData) {
      return new Promise((resolve, reject) => {
        axios({
          url: `/api/kelas/materi/${materiData.get('id')}`,
          method: 'POST',
          data: materiData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Delete Materi
    deleteMateri(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/api/kelas/materi/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Fetch Materi Text
    fetchMateriText(id) { return axios.get(`/api/kelas/materi/text/${id}`) },

    // 👉 Update Materi Text
    updateMateriText(materiData) {
      return new Promise((resolve, reject) => {
        axios({
          url: `/api/kelas/materi/text/${materiData.id}`,
          method: 'POST',
          data: materiData,
          headers: { 'Content-Type': 'application/json' },
        })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
  },
})
