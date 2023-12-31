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
    mdiSvg,
    mdiBagPersonalTagOutline,
    mdiClockOutline,
    mdiCloud,
    mdiCrop,
    mdiMessage,
    mdiAccountSwitch,
    mdiAccountKey,
    mdiCogs,
    mdiWeb,
    mdiBrightnessAuto,
    mdiFormatColumns, mdiGoodreads
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
                icon:  mdiGoodreads,
                route: "product.index",
                label: "products",
                roles: ['admin'],
            },
            {
                icon:  mdiViewWeek,
                route: "categories.index",
                label: "categories",
                roles: ['admin'],
            },
            {
                icon:  mdiSvg,
                route: "brand.index",
                label: "brands",
                roles: ['admin'],
            },
            {
                icon:   mdiBrightnessAuto,
                route: "attribute_group.index",
                label: "attribute_groups",
                roles: ['admin'],
            },
            {
                icon:  mdiFormatColumns,
                route: "feature.index",
                label: "features",
                roles: ['admin'],
            },
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
