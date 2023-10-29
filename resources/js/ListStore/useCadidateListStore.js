import axios from '@axios'
import { defineStore } from 'pinia'

export const useCadidateListStore = defineStore('CadidateListStore', {
  actions: {
    // 👉 Fetch users data
    fetchCadidates(params) { return axios.get('/api/cadidate', { params }) },

    // 👉 Add Cadidate
    addCadidate(cadidateData) {
      return new Promise((resolve, reject) => {
        axios({
          url: '/api/cadidate',
          method: 'POST',
          data: cadidateData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })

          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single user
    fetchCadidate(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/api/cadidate/${id}`).then(response => resolve(response.data.cadidate)).catch(error => reject(error))
      })
    },

    // 👉 Update Cadidate
    updateCadidate(cadidateData) {
      return new Promise((resolve, reject) => {
        axios({
          url: `/api/cadidate/${cadidateData.get('id')}`,
          method: 'POST',
          data: cadidateData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Delete Cadidate
    deleteCadidate(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/api/cadidate/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
