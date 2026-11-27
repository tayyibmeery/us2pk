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

// ============================================================
// ICON IMPORTS FOR LANDING PAGES
// ============================================================
// I'm using existing icons from the icon set. If you want
// custom icons, you can import them here.
const HeroIcon = GridIcon;       // Using GridIcon for Hero
const AboutIcon = UserCircleIcon; // Using UserCircleIcon for About
const ServiceIcon = BoxCubeIcon;  // Using BoxCubeIcon for Services
const TestimonialIcon = UserGroupIcon; // Using UserGroupIcon for Testimonials
const TeamIcon = UserGroupIcon;   // Using UserGroupIcon for Team
const PricingIcon = DollarLineIcon; // Using DollarLineIcon for Pricing
const FaqIcon = TaskIcon;         // Using TaskIcon for FAQ
const BlogIcon = DocsIcon;        // Using DocsIcon for Blog
const WhyUsIcon = FlagIcon;       // Using FlagIcon for Why Us
const ContactIcon = SendIcon;     // Using SendIcon for Contact
const FooterIcon = HomeIcon;      // Using HomeIcon for Footer
const StatsIcon = BarChartIcon;   // Using BarChartIcon for Stats
const QuoteIcon = SendIcon;       // Using SendIcon for Quotes
const ProhibitedIcon = FlagIcon;  // Using FlagIcon for Prohibited Items

// ============================================================
// MENU GROUPS
// ============================================================
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
    ],
  },
  {
    title: "Finance",
    items: [
      {
        icon: TableIcon,
        name: "Financial Statement",
        subItems: [
          { name: "Chart of Accounts", path: "/admin/accounts" },
          { name: "Vouchers", path: "/admin/vouchers" },
          { name: "Approval Screen", path: "/admin/vouchers/approval" },
          { name: "General Journal", path: "/admin/journal" },
          { name: "Ledger", path: "/admin/ledger" },
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
          { name: "Balance Sheet", path: "/admin/pandl/balance-sheet" },
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
      {
        icon: PlugInIcon,
        name: "Payment Methods",
        path: "/admin/payment-methods",
        adminOnly: true,
      },
      {
        icon: HomeIcon,
        name: "Shopping Sites",
        path: "/admin/sites",
        adminOnly: true,
      },
    ],
  },
  // ============================================================
  // LANDING PAGES - NEW GROUP
  // ============================================================
  {
    title: "Landing Pages",
    items: [
      {
        icon: HeroIcon,
        name: "Hero Slides",
        path: "/admin/hero-slides",
        adminOnly: true,
      },
      {
        icon: AboutIcon,
        name: "About Sections",
        path: "/admin/about-sections",
        adminOnly: true,
      },
      {
        icon: ServiceIcon,
        name: "Services",
        path: "/admin/services",
        adminOnly: true,
      },
      {
        icon: TestimonialIcon,
        name: "Testimonials",
        path: "/admin/testimonials",
        adminOnly: true,
      },
      {
        icon: TeamIcon,
        name: "Team Members",
        path: "/admin/team-members",
        adminOnly: true,
      },
      {
        icon: PricingIcon,
        name: "Pricing Plans",
        path: "/admin/pricing-plans",
        adminOnly: true,
      },
      {
        icon: FaqIcon,
        name: "FAQs",
        path: "/admin/faqs",
        adminOnly: true,
      },
      {
        icon: BlogIcon,
        name: "Blog Posts",
        path: "/admin/blog-posts",
        adminOnly: true,
      },
      {
        icon: WhyUsIcon,
        name: "Why Us Sections",
        path: "/admin/why-us-sections",
        adminOnly: true,
      },
      {
        icon: ContactIcon,
        name: "Contact Sections",
        path: "/admin/contact-sections",
        adminOnly: true,
      },
      {
        icon: FooterIcon,
        name: "Footer Sections",
        path: "/admin/footer-sections",
        adminOnly: true,
      },
      {
        icon: StatsIcon,
        name: "Statistics",
        path: "/admin/stats",
        adminOnly: true,
      },
      {
        icon: QuoteIcon,
        name: "Quote Requests",
        path: "/admin/quote-requests",
        adminOnly: true,
      },
      {
        icon: ProhibitedIcon,
        name: "Prohibited Items",
        path: "/admin/prohibited-items",
        adminOnly: true,
      },
    ],
  },
  // In the System section:
  {
    title: "System",
    items: [
      {
        icon: PageIcon,
        name: "Page Settings",
        path: "/admin/pages",
        adminOnly: true,
      },
    ],
  },
];

// Filter menu items based on role
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
