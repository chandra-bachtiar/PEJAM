/**
 * Return if user is logged in
 * This is completely up to you and how you want to store the token in your frontend application
 * e.g. If you are using cookies to store the application please update this function
 */ 
import axios from '@axios'

export const isUserLoggedIn = async () => {
  return axios.post('/api/auth/validate').then(response => response.data.valid).catch(() => false)
}
