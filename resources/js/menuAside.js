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
    mdiBagPersonalTagOutline, mdiClockOutline, mdiCloud, mdiCrop, mdiMessage,mdiAccountSwitch,mdiAccountKey
} from "@mdi/js";

export default [

    {
        route: "dashboard",
        icon: mdiMonitor,
        label: "Dashboard",
        roles: ['standard','admin'],
    },
    {
        icon:  mdiAccountMultiple,
        label: "Management",
        roles: ['admin'],
        menu: [
            {
                icon:  mdiAccountMultiple,
                route: "user.index",
                label: "Users",
                roles: ['admin'],
            },
            {
                route: "role.index",
                icon:  mdiAccountSwitch,
                label: "Roles",
                roles: ['admin']
            },
            {
                route: "permission.index",
                icon:  mdiAccountKey,
                label: "Permissions",
                roles: ['admin']
            }
        ]

    },
    {
        icon:  mdiStoreEdit,
        label: "Catalog",
        roles: ['admin'],
        menu: [
            {
                icon:  mdiViewWeek,
                route: "category.index",
                label: "Categories",
                roles: ['admin'],
            }
        ]

    },

    {
        route: "test.index",
        icon: mdiBagPersonalTagOutline,
        label: "Test",
        roles: []

    },

    {
        href: "https://example.com/",
        icon: mdiMonitor,
        label: "Example.com",
        roles: ['admin']
    },
    {
        route: "message.index",
        icon: mdiMessage,
        label: "Messages",
        roles: ['admin']
    },

];
