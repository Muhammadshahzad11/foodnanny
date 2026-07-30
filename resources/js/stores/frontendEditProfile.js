import axios from "axios";
import {defineStore} from "pinia";

export const useFrontendEditProfileStore = defineStore  ('frontendEditProfile', {
    state: () => ({
        profile: []
    }),
    actions: {
        updateProfile: function (payload) {
            return new Promise((resolve, reject) => {
                axios.put('/profile', payload).then(res => {
                    this.profile = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changeImage: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/profile/change-image`, payload.form, {
                        headers: {"Content-Type": "multipart/form-data"},
                    }
                ).then((res) => {
                    this.profile = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changePassword: function (payload) {
            return new Promise((resolve, reject) => {
                axios.put(`/profile/change-password`,payload).then((res) => {
                    this.profile = res.data.data;
                    resolve(res);
                }).catch((err) => {reject(err);});
            });
        }
    }
});
