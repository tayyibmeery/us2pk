<template>
  <aside :class="[
    'fixed mt-16 flex flex-col lg:mt-0 top-0 px-5 left-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-99999 border-r border-gray-200',
    {
      'lg:w-[290px]': isExpanded || isMobileOpen || isHovered,
      'lg:w-[90px]': !isExpanded && !isHovered,
      'translate-x-0 w-[290px]': isMobileOpen,
      '-translate-x-full': !isMobileOpen,
      'lg:translate-x-0': true,
    },
  ]" @mouseenter="!isExpanded && (isHovered = true)" @mouseleave="isHovered = false">
    <div :class="[
      'py-8 flex',
      !isExpanded && !isHovered ? 'lg:justify-center' : 'justify-start',
    ]">
      <router-link to="/">
        <img v-if="isExpanded || isHovered || isMobileOpen" class="dark:hidden" src="/images/logo/logo.png" alt="Logo"
          width="150" height="40" />
        <img v-if="isExpanded || isHovered || isMobileOpen" class="hidden dark:block" src="/images/logo/logo-dark.svg"
          alt="Logo" width="150" height="40" />
        <img v-else src="/images/logo/logo-icon.png" alt="Logo" width="32" height="32" />
      </router-link>
    </div>
    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
      <nav class="mb-6">
        <div class="flex flex-col gap-4">
          <div v-for="(menuGroup, groupIndex) in filteredMenuGroups" :key="groupIndex">
            <h2 :class="[
              'mb-4 text-xs uppercase flex leading-[20px] text-gray-400',
              !isExpanded && !isHovered ? 'lg:justify-center' : 'justify-start',
            ]">
              <template v-if="isExpanded || isHovered || isMobileOpen">
                {{ menuGroup.title }}
              </template>
              <HorizontalDots v-else />
            </h2>
            <ul class="flex flex-col gap-4">
              <li v-for="(item, index) in menuGroup.items" :key="item.name">
                <button v-if="item.subItems" @click="toggleSubmenu(groupIndex, index)" :class="[
                  'menu-item group w-full',
                  {
                    'menu-item-active': isSubmenuOpen(groupIndex, index),
                    'menu-item-inactive': !isSubmenuOpen(groupIndex, index),
                  },
                  !isExpanded && !isHovered ? 'lg:justify-center' : 'lg:justify-start',
                ]">
                  <span :class="[
                    isSubmenuOpen(groupIndex, index)
                      ? 'menu-item-icon-active'
                      : 'menu-item-icon-inactive',
                  ]">
                    <component :is="item.icon" />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">{{ item.name }}</span>
                  <ChevronDownIcon v-if="isExpanded || isHovered || isMobileOpen" :class="[
                    'ml-auto w-5 h-5 transition-transform duration-200',
                    {
                      'rotate-180 text-brand-500': isSubmenuOpen(groupIndex, index),
                    },
                  ]" />
                </button>
                <router-link v-else-if="item.path" :to="item.path" :class="[
                  'menu-item group',
                  {
                    'menu-item-active': isActive(item.path),
                    'menu-item-inactive': !isActive(item.path),
                  },
                ]">
                  <span :class="[
                    isActive(item.path)
                      ? 'menu-item-icon-active'
                      : 'menu-item-icon-inactive',
                  ]">
                    <component :is="item.icon" />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">{{ item.name }}</span>
                </router-link>
                <transition @enter="startTransition" @after-enter="endTransition" @before-leave="startTransition"
                  @after-leave="endTransition">
                  <div v-show="isSubmenuOpen(groupIndex, index) &&
                    (isExpanded || isHovered || isMobileOpen)
                    ">
                    <ul class="mt-2 space-y-1 ml-9">
                      <li v-for="subItem in item.subItems" :key="subItem.name">
                        <router-link :to="subItem.path" :class="[
                          'menu-dropdown-item',
                          {
                            'menu-dropdown-item-active': isActive(subItem.path),
                            'menu-dropdown-item-inactive': !isActive(subItem.path),
                          },
                        ]">
                          {{ subItem.name }}
                          <span class="flex items-center gap-1 ml-auto">
                            <span v-if="subItem.new" :class="[
                              'menu-dropdown-badge',
                              {
                                'menu-dropdown-badge-active': isActive(subItem.path),
                                'menu-dropdown-badge-inactive': !isActive(subItem.path),
                              },
                            ]">
                              new
                            </span>
                            <span v-if="subItem.pro" :class="[
                              'menu-dropdown-badge',
                              {
                                'menu-dropdown-badge-active': isActive(subItem.path),
                                'menu-dropdown-badge-inactive': !isActive(subItem.path),
                              },
                            ]">
                              pro
                            </span>
                          </span>
                        </router-link>
                      </li>
                    </ul>
                  </div>
                </transition>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/authStore";
import UserGroupIcon from '@/icons/UserGroupIcon.vue';
import {
  GridIcon,
  UserCircleIcon,
  PieChartIcon,
  ChevronDownIcon,
  HorizontalDots,
  BoxCubeIcon,
  ListIcon,
  TaskIcon,
  FolderIcon,
  PlugInIcon,
  BoxIcon,
  DollarLineIcon,
  TableIcon,
  DocsIcon,
  BarChartIcon,
  FlagIcon,
  PageIcon,
  SendIcon,
  SettingsIcon,
  HomeIcon,
} from "../../icons";
import { useSidebar } from "@/composables/useSidebar";

const route = useRoute();
const authStore = useAuthStore();
const isAdmin = computed(() => authStore.user?.role === 'admin');

const { isExpanded, isMobileOpen, isHovered, openSubmenu } = useSidebar();

// Menu is grouped by workflow area (Overview -> Logistics -> Finance -> Directory -> System)
// so related items sit together instead of being scattered by CRUD type.
const menuGroups = [
  {
    title: "Overview",
    items: [
      {
        icon: GridIcon,
        name: "Dashboard",
        path: "/admin/dashboard",
        adminOnly: true,
      },
      {
        icon: UserCircleIcon,
        name: "User Profile",
        path: "/profile",
      },
      {
        icon: BoxCubeIcon,
        name: "My Shipments",
        path: "/my-shipments",
        userOnly: true,
      },
    ],
  },
  {
    title: "Logistics",
    items: [
      {
        icon: BoxCubeIcon,
        name: "Shipments",
        path: "/admin/shipments",
        adminOnly: true,
      },
      {
        icon: ListIcon,
        name: "Consolidations",
        path: "/admin/consolidations",
        adminOnly: true,
      },
      {
        icon: TaskIcon,
        name: "Shipment Statuses",
        path: "/admin/shipment-statuses",
        adminOnly: true,
      },
      {
        icon: FolderIcon,
        name: "Warehouses",
        path: "/admin/warehouses",
        adminOnly: true,
      },
      {
        icon: SendIcon,
        name: "International Couriers",
        path: "/admin/international-couriers",
        adminOnly: true,
      },
      {
        icon: BoxIcon,
        name: "Local Couriers",
        path: "/admin/local-couriers",
        adminOnly: true,
      },
    ],
  },
  {
    title: "Finance",
    items: [
      {
        icon: DollarLineIcon,
        name: "Accounting",
        subItems: [
          { name: "Dashboard", path: "/admin/accounting" },

        ],
        adminOnly: true,
      },
      {
        icon: TableIcon,
        name: "Financial Statement",
        subItems: [
          { name: "Chart of Accounts", path: "/admin/accounts" },
          { name: "Vouchers", path: "/admin/vouchers" },
          { name: "Approval Screen", path: "/admin/vouchers/approval" },
          { name: "General Journal", path: "/admin/journal" },
          { name: "Trial Balance", path: "/admin/trial-balance" },
        ],
        adminOnly: true,
      },
      {
        icon: PieChartIcon,
        name: "P&L",
        subItems: [
          { name: "Since Inception", path: "/admin/pandl/since-inception" },
          { name: "Annual", path: "/admin/pandl/yearly" },
          { name: "Quarterly", path: "/admin/pandl/quarterly" },
          { name: "Monthly", path: "/admin/pandl/monthly" },
          { name: "Balance Sheet", path: "/admin/balance-sheet" },
        ],
        adminOnly: true,
      },
      {
        icon: DocsIcon,
        name: "Invoices",
        path: "/admin/invoices",
        adminOnly: true,
      },
      {
        icon: BarChartIcon,
        name: "Revenues",
        path: "/admin/revenues",
        adminOnly: true,
      },
      {
        icon: UserCircleIcon,
        name: "Debtors",
        path: "/admin/debtors",
        adminOnly: true,
      },
    ],
  },
  {
    title: "Directory & Setup",
    items: [
      {
        icon: UserGroupIcon,
        name: "Users",
        path: "/admin/users",
        adminOnly: true,
      },
      {
        icon: GridIcon,
        name: "Cities",
        path: "/admin/cities",
        adminOnly: true,
      },
      {
        icon: PlugInIcon,
        name: "Payment Methods",
        path: "/admin/payment-methods",
        adminOnly: true,
      },
      {
        icon: HomeIcon,
        name: "Sites",
        path: "/admin/sites",
        adminOnly: true,
      },
      // {
      //   icon: FlagIcon,
      //   name: "Weight Discounts",
      //   path: "/admin/weight-discounts",
      //   adminOnly: true,
      // },
    ],
  },
  {
    title: "System",
    items: [
      {
        icon: SettingsIcon,
        name: "Settings",
        path: "/admin/settings",
        adminOnly: true,
      },
      {
        icon: PageIcon,
        name: "Pages",
        path: "/admin/pages",
        adminOnly: true,
      },
    ],
  },
];

// Filter menu items based on roleT
const filteredMenuGroups = computed(() => {
  return menuGroups
    .map(group => ({
      ...group,
      items: group.items.filter(item => {
        if (item.adminOnly && !isAdmin.value) return false;
        if (item.userOnly && isAdmin.value) return false;
        return true;
      }),
    }))
    // Hide any group that ends up with zero visible items (e.g. all-admin
    // groups for a non-admin user) so we don't render an empty heading.
    .filter(group => group.items.length > 0);
});

const isActive = (path) => route.path === path;

const toggleSubmenu = (groupIndex, itemIndex) => {
  const key = `${groupIndex}-${itemIndex}`;
  openSubmenu.value = openSubmenu.value === key ? null : key;
};

const isAnySubmenuRouteActive = computed(() => {
  return menuGroups.some((group) =>
    group.items.some(
      (item) =>
        item.subItems && item.subItems.some((subItem) => isActive(subItem.path))
    )
  );
});

const isSubmenuOpen = (groupIndex, itemIndex) => {
  const key = `${groupIndex}-${itemIndex}`;
  return (
    openSubmenu.value === key ||
    (isAnySubmenuRouteActive.value &&
      menuGroups[groupIndex].items[itemIndex].subItems?.some((subItem) =>
        isActive(subItem.path)
      ))
  );
};

const startTransition = (el) => {
  el.style.height = "auto";
  const height = el.scrollHeight;
  el.style.height = "0px";
  el.offsetHeight; // force reflow
  el.style.height = height + "px";
};

const endTransition = (el) => {
  el.style.height = "";
};
</script>
