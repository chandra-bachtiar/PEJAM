import axios from '@axios'
import { defineStore } from 'pinia'

export const useTrainerListStore = defineStore('TrainerListStore', {
  actions: {
    // 👉 Fetch trainers data
    fetchTrainers(params) { return axios.get('/api/account/trainer', { params }) },

    // 👉 Add Trainer
    addTrainer(trainerData) {
      return new Promise((resolve, reject) => {
        axios({
          url: '/api/account/trainer',
          method: 'POST',
          data: trainerData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })

          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single trainer
    fetchTrainer(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/api/account/trainer/${id}`).then(response => resolve(response.data.trainer)).catch(error => reject(error))
      })
    },

    // 👉 Update Trainer
    updateTrainer(trainerData) {
      return new Promise((resolve, reject) => {
        axios({
          url: `/api/account/trainer/${trainerData.get('id')}`,
          method: 'POST',
          data: trainerData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Delete Trainer
    deleteTrainer(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/api/account/trainer/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
