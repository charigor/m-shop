import {
    mdiAccountCircle,
    mdiMonitor,
    mdiGithub,
    mdiLock,
    mdiAlertCircle,
    mdiSquareEditOutline,
    mdiTable,
    mdiViewList,
    mdiTelevisionGuide,
    mdiResponsive,
    mdiPalette,
    mdiReact,
    mdiAccountMultiple,
    mdiStoreEdit,
    mdiViewWeek,
    mdiBagPersonalTagOutline, mdiClockOutline, mdiCloud, mdiCrop, mdiMessage,mdiAccountSwitch,mdiAccountKey,mdiCogs,mdiWeb
} from "@mdi/js";

export default [

    {
        route: "dashboard",
        icon: mdiMonitor,
        label: "dashboard",
        roles: ['standard','admin'],
    },
    {
        icon:  mdiAccountMultiple,
        label: "management",
        roles: ['admin'],
        menu: [
            {
                icon:  mdiAccountMultiple,
                route: "user.index",
                label: "users",
                roles: ['admin'],
            },
            {
                route: "role.index",
                icon:  mdiAccountSwitch,
                label: "roles",
                roles: ['admin']
            },
            {
                route: "permission.index",
                icon:  mdiAccountKey,
                label: "permissions",
                roles: ['admin']
            }
        ]

    },
    {
        icon:  mdiStoreEdit,
        label: "catalog",
        roles: ['admin'],
        menu: [
            {
                icon:  mdiViewWeek,
                route: "category.index",
                label: "categories",
                roles: ['admin'],
            }
        ]

    },

    {
        route: "message.index",
        icon: mdiMessage,
        label: "messages",
        roles: ['admin']
    },
    {
        icon: mdiCogs,
        label: "settings",
        roles: ['admin'],
        menu: [
            {
                icon:  mdiWeb,
                route: "lang.index",
                label: "languages",
                roles: ['admin'],
            },
        ]
    }

];
