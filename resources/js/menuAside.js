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
        route: "role.index",
        icon:  mdiAccountMultiple,
        label: "Roles",
        roles: ['admin']

    },

    {
        route: "test.index",
        icon: mdiBagPersonalTagOutline,
        label: "Test",
        roles: []

    },
    // {
    //     route: "another-route-name",
    //     icon: mdiMonitor,
    //     label: "Dashboard 2",
    // },
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
  // {
  //   to: "/dashboard",
  //   icon: mdiMonitor,
  //   label: "Dashboard",
  // },
  // {
  //   to: "/tables",
  //   label: "Tables",
  //   icon: mdiTable,
  // },
  // {
  //   to: "/forms",
  //   label: "Forms",
  //   icon: mdiSquareEditOutline,
  // },
  // {
  //   to: "/ui",
  //   label: "UI",
  //   icon: mdiTelevisionGuide,
  // },
  // {
  //   to: "/responsive",
  //   label: "Responsive",
  //   icon: mdiResponsive,
  // },
  // {
  //   to: "/",
  //   label: "Styles",
  //   icon: mdiPalette,
  // },
  // {
  //   to: "/profile",
  //   label: "Profile",
  //   icon: mdiAccountCircle,
  // },
  // {
  //   to: "/login",
  //   label: "Login",
  //   icon: mdiLock,
  // },
  // {
  //   to: "/error",
  //   label: "Error",
  //   icon: mdiAlertCircle,
  // },
  // {
  //   label: "Dropdown",
  //   icon: mdiViewList,
  //   menu: [
  //     {
  //       label: "Item One",
  //     },
  //     {
  //       label: "Item Two",
  //     },
  //   ],
  // },
  // {
  //   href: "https://github.com/justboil/admin-one-vue-tailwind",
  //   label: "GitHub",
  //   icon: mdiGithub,
  //   target: "_blank",
  // },
  // {
  //   href: "https://github.com/justboil/admin-one-react-tailwind",
  //   label: "React version",
  //   icon: mdiReact,
  //   target: "_blank",
  // },
];
