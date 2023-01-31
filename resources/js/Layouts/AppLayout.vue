<template>
  <a-layout style="min-height: 100vh">
    <a-layout-sider
      breakpoint="lg"
      collapsed-width="0"
      @collapse="onCollapse"
      @breakpoint="onBreakpoint"
    >
      <div class="logo">
        <h2 class="logotext">MZ-Audit</h2>
      </div>
      <a-menu theme="dark" mode="inline" v-model:selectedKeys="selectedKeys">
        <a-menu-item key="1">
          <jet-nav-link
            :href="route('companies')"
            :active="route().current('companies')"
          >
            <BankFilled class="mr-2" />
            Companies
          </jet-nav-link>
        </a-menu-item>
        <a-menu-item key="2" v-if="this.$page.props.co_id">
          <desktop-outlined />
          <jet-nav-link
            :href="route('years')"
            :active="route().current('years')"
          >
            <CalendarFilled class="mr-2" />
            Years
          </jet-nav-link>
        </a-menu-item>
        <a-menu-item
          key="3"
          v-if="this.$page.props.co_id && this.$page.props.yr_id"
        >
          <jet-nav-link
            :href="route('teams')"
            :active="route().current('teams')"
          >
            <UserAddOutlined class="mr-2" />
            Teams
          </jet-nav-link>
        </a-menu-item>
        <a-menu-item
          key="4"
          v-if="
            this.$page.props.co_id &&
            this.$page.props.yr_id &&
            this.$page.props.team_id
          "
        >
          <jet-nav-link
            :href="route('users')"
            :active="route().current('users')"
          >
            <UsergroupAddOutlined class="mr-2" />
            Users
          </jet-nav-link>
        </a-menu-item>

        <a-menu-item
          key="5"
          v-if="
            this.$page.props.co_id &&
            this.$page.props.yr_id &&
            this.$page.props.team_id
          "
        >
          <jet-nav-link
            :href="route('accountgroups')"
            :active="route().current('accountgroups')"
          >
            <GroupOutlined class="mr-2" />
            Account Groups
          </jet-nav-link>
        </a-menu-item>

        <a-menu-item
          key="6"
          v-if="
            this.$page.props.co_id &&
            this.$page.props.yr_id &&
            this.$page.props.team_id
          "
        >
          <jet-nav-link
            :href="route('accounts')"
            :active="route().current('accounts')"
          >
            <AccountBookOutlined class="mr-2" />
            Accounts
          </jet-nav-link>
        </a-menu-item>

        <a-menu-item
          key="7"
          v-if="
            this.$page.props.co_id &&
            this.$page.props.yr_id &&
            this.$page.props.team_id
          "
        >
          <jet-nav-link
            :href="route('trial.index')"
            :active="route().current('trial.index')"
          >
            <UploadOutlined class="mr-2" />
            Upload TB
          </jet-nav-link>
        </a-menu-item>

        <a-menu-item
          key="8"
          v-if="
            this.$page.props.co_id &&
            this.$page.props.yr_id &&
            this.$page.props.team_id
          "
        >
          <jet-nav-link
            :href="route('filing', ['planing'])"
            :active="route().current('filing.planing')"
          >
            <FileOutlined class="mr-2" />
            Planning
          </jet-nav-link>
        </a-menu-item>

        <a-menu-item
          key="9"
          v-if="
            this.$page.props.co_id &&
            this.$page.props.yr_id &&
            this.$page.props.team_id
          "
        >
          <jet-nav-link
            :href="route('filing', ['completion'])"
            :active="route().current('filing.completion')"
          >
            <FileOutlined class="mr-2" />
            Completion
          </jet-nav-link>
        </a-menu-item>

        <a-menu-item
          key="10"
          v-if="
            this.$page.props.co_id &&
            this.$page.props.yr_id &&
            this.$page.props.team_id
          "
        >
          <jet-nav-link
            :href="route('filing', ['execution'])"
            :active="route().current('filing.execution')"
            ><FolderOpenOutlined class="mr-2" />
            Execution
          </jet-nav-link>
        </a-menu-item>

        <a-menu-item
          key="11"
          v-if="
            this.$page.props.co_id &&
            this.$page.props.yr_id &&
            this.$page.props.team_id
          "
        >
          <jet-nav-link
            :href="route('details')"
            :active="route().current('details')"
          >
            <FileOutlined class="mr-2" />
            Details
          </jet-nav-link>
        </a-menu-item>

        <a-sub-menu key="sub1">
          <template #title>
            <span><SettingOutlined class="mr-2" />Setting</span>
          </template>
          <a-menu-item key="12">
            <jet-nav-link
              :href="route('profile.show')"
              :active="route().current('profile.show')"
            >
              <UserOutlined class="mr-2" />
              Profile
            </jet-nav-link>
          </a-menu-item>
          <a-menu-item key="13">
            <jet-nav-link @click="logout">
              <LogoutOutlined class="mr-2" />
              Logout
            </jet-nav-link>
          </a-menu-item>
        </a-sub-menu>
      </a-menu>
    </a-layout-sider>
    <a-layout>
      <!-- :style="{ background: '#fff', padding: 0 }" -->
      <a-layout-header v-if="$slots.header">
        <!-- <header v-if="$slots.header"> -->
        <slot name="header" />
        <!-- </header> -->
      </a-layout-header>
      <FlashMessage />

      <a-layout-content :style="{ margin: '24px 16px 0' }">
        <div style="margin-bottom: 10px">
          <Breadcrumb>
            <Select
              :options="years"
              :field-names="{ label: 'end', value: 'id' }"
              filterOption="true"
              v-model:value="selectedyear"
              optionFilterProp="end"
              mode="single"
              placeholder="Please select Year"
              showArrow
              @change="yrch"
              class="w-1/2"
            />
            <Select
              :options="options"
              :field-names="{ label: 'name', value: 'id' }"
              filterOption="true"
              v-model:value="selected"
              optionFilterProp="name"
              mode="single"
              placeholder="Please select Company"
              showArrow
              @change="coch"
              class="w-1/2"
            />
          </Breadcrumb>
        </div>
        <div
          :style="{ padding: '24px', background: '#fff', minHeight: '360px' }"
        >
          <slot />
        </div>
      </a-layout-content>
      <a-layout-footer style="text-align: center">
        MZ-AUDIT ©2023 Created by Digital Solution Department
      </a-layout-footer>
    </a-layout>
  </a-layout>
</template>

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>
<script>
import {
  UserAddOutlined,
  UserOutlined,
  CalendarFilled,
  BankFilled,
  UsergroupAddOutlined,
  UploadOutlined,
  FileOutlined,
  AccountBookOutlined,
  FolderOpenOutlined,
  GroupOutlined,
  LogoutOutlined,
  SettingOutlined,
} from "@ant-design/icons-vue";
import ApplicationLogo from "@/Jetstream/ApplicationLogo";
import JetNavLink from "@/Jetstream/NavLink";
import { Layout, Menu, Select } from "ant-design-vue";
import "ant-design-vue/dist/antd.css";
import FlashMessage from "@/Layouts/FlashMessage";

export default {
  components: {
    "a-layout": Layout,
    "a-layout-sider": Layout.Sider,
    "a-layout-header": Layout.Header,
    "a-layout-content": Layout.Content,
    "a-layout-footer": Layout.Footer,
    "a-menu": Menu,
    "a-menu-item": Menu.Item,
    "a-sub-menu": Menu.SubMenu,
    JetNavLink,
    ApplicationLogo,
    FlashMessage,

    //All Icon Export
    UserAddOutlined,
    CalendarFilled,
    BankFilled,
    UsergroupAddOutlined,
    UploadOutlined,
    FileOutlined,
    FolderOpenOutlined,
    AccountBookOutlined,
    GroupOutlined,
    UserOutlined,
    SettingOutlined,
    LogoutOutlined,
    Select,
  },

  //   props: {
  //     companies: Object,
  //     company: Object,
  //     year: Object,
  //     years: Object,
  //   },

  data() {
    return {
      // usePage().props.auth.user
      // co_id: this.$page.props.co_id,
      co_id: this.$page.props.company,
      options: this.$page.props.companies,
      selected: this.$page.props.company.name,
      selectedyear: this.$page.props.year.end,
      yr_id: this.$page.props.year,
      years: this.$page.props.years,
    };
  },

  methods: {
    coch(value) {
      //   alert(value);
      this.$inertia.get(route("companies.coch", value));
    },
    yrch(value) {
      this.$inertia.get(route("years.yrch", value));
    },
    logout() {
      this.$inertia.post(route("logout"));
    },
  },
};
</script>

<style>
.ant-layout-sider-children .logo {
  /* height: 32px; */
  background: rgba(255, 255, 255, 0.2);
  margin: 16px;
}

.ant-layout-sider-children .logo .logotext {
  color: #fff;
  font-size: 25px;
  text-align: center;
  font-weight: bold;
  font-family: ui-serif;
}

.site-layout-sub-header-background {
  background: #141414;
}

.site-layout-background {
  background: #fff;
}

[data-theme="dark"] .site-layout-sub-header-background {
  background: #141414;
}

.ant-layout-header h2 {
  margin-top: 0;
  margin-bottom: 0.5em;
  color: white;
  font-weight: bold;
  font-size: 18px;
}
</style>
