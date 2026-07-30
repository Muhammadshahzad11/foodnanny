const ProfileEditProfileComponent    = () => import("../../components/admin/profile/ProfileEditProfileComponent.vue");
const ProfileChangePasswordComponent = () => import("../../components/admin/profile/ProfileChangePasswordComponent.vue");

export default [
    {
        path: "/admin/profile/edit-profile",
        component: ProfileEditProfileComponent,
        name: "admin.profile.editProfile",
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "",
            breadcrumb: "edit_profile",
            access : true
        },
    },
    {
        path: "/admin/profile/change-password",
        component: ProfileChangePasswordComponent,
        name: "admin.profile.changePassword",
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "",
            breadcrumb: "change_password",
            access : true
        },
    }
];
