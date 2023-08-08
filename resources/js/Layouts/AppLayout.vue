<template>
    <a-layout style="min-height: 100vh">
        <a-layout-sider breakpoint="lg" collapsed-width="0" @collapse="onCollapse" @breakpoint="onBreakpoint">
            <div class="logo">
                <h2 class="logotext">MZ-AMS</h2>
            </div>

            <a-menu theme="dark" mode="inline" v-model:selectedKeys="selectedKeys">
                <a-menu-item key="1">
                    <jet-nav-link :href="route('companies')" :active="route().current('companies')">
                        <BankFilled class="mr-2" />
                        Companies
                    </jet-nav-link>
                </a-menu-item>
                <a-menu-item key="2" v-if="this.$page.props.co_id">
                    <desktop-outlined />
                    <jet-nav-link :href="route('years')" :active="route().current('years')">
                        <CalendarFilled class="mr-2" />
                        Years
                    </jet-nav-link>
                </a-menu-item>
                <a-menu-item key="3" v-if="this.$page.props.co_id && this.$page.props.yr_id">
                    <jet-nav-link :href="route('teams')" :active="route().current('teams')">
                        <UserAddOutlined class="mr-2" />
                        Teams
                    </jet-nav-link>
                </a-menu-item>

                <a-sub-menu v-if="this.$page.props.co_id && this.$page.props.yr_id" key="sub2">
                    <template #title>
                        <span>
                            <CarryOutOutlined class="mr-2" />Confirmation
                        </span>
                    </template>
                    <a-sub-menu key="subsub1">
                        <template #title>
                            <span>
                                <AccountBookOutlined class="mr-2" />Advisor
                                Account
                            </span>
                        </template>
                        <a-menu-item key="14">
                            <jet-nav-link :href="route('advisors')" :active="route().current('advisors')">
                                <UserOutlined class="mr-2" />
                                Advisors
                            </jet-nav-link>
                        </a-menu-item>
                        <a-menu-item key="15">
                            <jet-nav-link :href="route('advisor_accounts')" :active="route().current('advisor_accounts')">
                                <AccountBookOutlined class="mr-2" />
                                Advisor Account
                            </jet-nav-link>
                        </a-menu-item>
                    </a-sub-menu>
                    <a-sub-menu key="subsub2">
                        <template #title>
                            <span>
                                <BankFilled class="mr-2" />Bank Detail
                            </span>
                        </template>
                        <a-menu-item key="16">
                            <jet-nav-link :href="route('banks')" :active="route().current('banks')">
                                <BankOutlined class="mr-2" />

                                Banks
                            </jet-nav-link>
                        </a-menu-item>
                        <a-menu-item key="17">
                            <jet-nav-link :href="route('branches')" :active="route().current('branches')">
                                <BranchesOutlined class="mr-2" />
                                Branches
                            </jet-nav-link>
                        </a-menu-item>
                        <a-menu-item key="18">
                            <jet-nav-link :href="route('bank_accounts')" :active="route().current('bank_accounts')">
                                <AccountBookOutlined class="mr-2" />
                                Bank Accounts
                            </jet-nav-link>
                        </a-menu-item>
                    </a-sub-menu>
                    <a-menu-item key="19">
                        <jet-nav-link v-if="
                            this.$page.props.co_id && this.$page.props.yr_id
                        " :href="route('balances')" :active="route().current('balances')">
                            <BankFilled class="mr-2" />
                            Bank Balances
                        </jet-nav-link>
                    </a-menu-item>
                    <a-menu-item key="20">
                        <jet-nav-link v-if="
                            this.$page.props.co_id && this.$page.props.yr_id
                        " :href="route('confirmations')" :active="route().current('confirmations')">
                            <CarryOutOutlined class="mr-2" />
                            Bank Confirmations
                        </jet-nav-link>
                    </a-menu-item>
                    <a-menu-item key="21">
                        <jet-nav-link v-if="
                            this.$page.props.co_id && this.$page.props.yr_id
                        " :href="route('advisor_confirmations')"
                            :active="route().current('advisor_confirmations')">
                            <CheckOutlined class="mr-2" />
                            Advisor Confirmations
                        </jet-nav-link>
                    </a-menu-item>
                </a-sub-menu>

                <a-menu-item key="5" v-if="
                    this.$page.props.co_id &&
                    this.$page.props.yr_id &&
                    this.$page.props.team_id
                ">
                    <jet-nav-link :href="route('accountgroups')" :active="route().current('accountgroups')">
                        <GroupOutlined class="mr-2" />
                        Account Groups
                    </jet-nav-link>
                </a-menu-item>

                <a-menu-item key="6" v-if="
                    this.$page.props.co_id &&
                    this.$page.props.yr_id &&
                    this.$page.props.team_id
                ">
                    <jet-nav-link :href="route('accounts')" :active="route().current('accounts')">
                        <AccountBookOutlined class="mr-2" />
                        Accounts
                    </jet-nav-link>
                </a-menu-item>

                <a-menu-item key="7" v-if="
                    this.$page.props.co_id &&
                    this.$page.props.yr_id &&
                    this.$page.props.team_id
                ">
                    <jet-nav-link :href="route('trial.index')" :active="route().current('trial.index')">
                        <UploadOutlined class="mr-2" />
                        Upload TB
                    </jet-nav-link>
                </a-menu-item>

                 <a-menu-item key="23" v-if="
                    this.$page.props.co_id &&
                    this.$page.props.yr_id &&
                    this.$page.props.team_id
                ">
                    <jet-nav-link :href="route('materialities')" :active="route().current('materialities')">
                        <VerticalAlignBottomOutlined class="mr-2" />
                       Materiality
                    </jet-nav-link>
                </a-menu-item>

                <a-menu-item key="8" v-if="
                    this.$page.props.co_id &&
                    this.$page.props.yr_id &&
                    this.$page.props.team_id
                ">
                    <jet-nav-link :href="route('filing', ['planing'])" :active="route().current('filing.planing')">
                        <FileOutlined class="mr-2" />
                        Planning
                    </jet-nav-link>
                </a-menu-item>

                <a-menu-item key="9" v-if="
                    this.$page.props.co_id &&
                    this.$page.props.yr_id &&
                    this.$page.props.team_id
                ">
                    <jet-nav-link :href="route('filing', ['completion'])" :active="route().current('filing.completion')">
                        <FileOutlined class="mr-2" />
                        Completion
                    </jet-nav-link>
                </a-menu-item>

                <a-menu-item key="10" v-if="
                    this.$page.props.co_id &&
                    this.$page.props.yr_id &&
                    this.$page.props.team_id
                ">
                    <jet-nav-link :href="route('filing', ['execution'])" :active="route().current('filing.execution')">
                        <FolderOpenOutlined class="mr-2" />
                        Execution
                    </jet-nav-link>
                </a-menu-item>

                <a-menu-item key="11" v-if="
                    this.$page.props.co_id &&
                    this.$page.props.yr_id &&
                    this.$page.props.team_id
                ">
                    <jet-nav-link :href="route('details')" :active="route().current('details')">
                        <FileOutlined class="mr-2" />
                        Details
                    </jet-nav-link>
                </a-menu-item>

                <a-menu-item key="22" v-if="
                    this.$page.props.co_id &&
                    this.$page.props.yr_id &&
                    this.$page.props.team_id
                ">
                    <jet-nav-link :href="route('index_temp', ['Report'])" :active="route().current('index_temp.report')">
                        <FileOutlined class="mr-2" />
                        Audit Reports
                    </jet-nav-link>
                </a-menu-item>

                <a-menu-item key="4" v-if="
                    this.$page.props.co_id &&
                    this.$page.props.yr_id &&
                    this.$page.props.team_id
                ">
                    <jet-nav-link :href="route('users')" :active="route().current('users')">
                        <UsergroupAddOutlined class="mr-2" />
                        Users
                    </jet-nav-link>
                </a-menu-item>

                <a-sub-menu key="sub1">
                    <template #title>
                        <span>
                            <SettingOutlined class="mr-2" />Setting
                        </span>
                    </template>
                    <a-menu-item key="12">
                        <jet-nav-link :href="route('profile.show')" :active="route().current('profile.show')">
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
        <a-layout :style="{ height: '100vh' }">
            <!-- :style="{ background: '#fff', padding: 0 }" -->
            <a-layout-header v-if="$slots.header">
                <!-- <header v-if="$slots.header"> -->
                <slot name="header" />
                <!-- </header> -->
            </a-layout-header>
            <FlashMessage />

            <a-layout-content :style="{ margin: '12px 16px 0' }">
                <div :style="{ marginBottom: '10px' }">
                    <Breadcrumb>
                        <!-- <a-divider orientation="left">Normal</a-divider>
                        <a-row type="flex">
                            <a-col :span="6" :order="4"
                                ><Select
                                    :options="options"
                                    :field-names="{
                                        label: 'name',
                                        value: 'id',
                                    }"
                                    show-search
                                    filterOption="true"
                                    v-model:value="selected"
                                    optionFilterProp="name"
                                    mode="single"
                                    placeholder="Please select Company"
                                    showArrow
                                    @change="coch"
                                    class="w-full"
                            /></a-col>
                            <a-col :span="6" :order="3"
                                ><Select
                                    :options="years"
                                    :field-names="{ label: 'end', value: 'id' }"
                                    show-search
                                    filterOption="true"
                                    v-model:value="selectedyear"
                                    optionFilterProp="end"
                                    mode="single"
                                    placeholder="Please select Year"
                                    showArrow
                                    @change="yrch"
                                    class="w-full"
                            /></a-col>
                            <a-col :span="6" :order="2">3 col-order-2</a-col>
                            <a-col :span="6" :order="1">4 col-order-1</a-col>
                        </a-row>
                        <a-divider orientation="left">Responsive</a-divider> -->
                        <a-row type="flex">
                            <a-col :span="6" :xs="{ order: 1 }" :sm="{ order: 2 }" :md="{ order: 3 }" :lg="{ order: 4 }">
                                <Select :options="options" :field-names="{
                                    label: 'name',
                                    value: 'id',
                                }" show-search filterOption="true" v-model:value="selected" optionFilterProp="name"
                                    mode="single" placeholder="Please select Company" showArrow @change="coch"
                                    class="w-full" />
                            </a-col>
                            <a-col :span="6" :xs="{ order: 2 }" :sm="{ order: 1 }" :md="{ order: 4 }" :lg="{ order: 3 }">
                                <Select :options="years" :field-names="{ label: 'end', value: 'id' }" show-search
                                    filterOption="true" v-model:value="selectedyear" optionFilterProp="end" mode="single"
                                    placeholder="Please select Year" showArrow @change="yrch" class="w-full" />
                            </a-col>
                            <a-col :span="6" :xs="{ order: 3 }" :sm="{ order: 4 }" :md="{ order: 2 }"
                                :lg="{ order: 2 }"></a-col>
                            <a-col :span="6" :xs="{ order: 4 }" :sm="{ order: 3 }" :md="{ order: 1 }"
                                :lg="{ order: 1 }"></a-col>
                        </a-row>
                    </Breadcrumb>
                </div>
                <div :style="{
                    overflow: 'auto',
                    padding: '24px',
                    background: '#fff',
                    minHeight: '360px',
                    height: '94%',
                }">
                    <slot />
                </div>
            </a-layout-content>
            <a-layout-footer style="text-align: center">
                MZ-AMS ©2023 Created by Digital Solution Department
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
    CarryOutOutlined,
    BankOutlined,
    CheckOutlined,
    BranchesOutlined,
    VerticalAlignBottomOutlined,
} from "@ant-design/icons-vue";
import ApplicationLogo from "@/Jetstream/ApplicationLogo";
import JetNavLink from "@/Jetstream/NavLink";
import { Layout, Menu, Select, Button, Row, Col } from "ant-design-vue";
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
        "a-button": Button,
        "a-row": Row,
        "a-col": Col,
        JetNavLink,
        ApplicationLogo,
        FlashMessage,

        //All Icon Export
        VerticalAlignBottomOutlined,
        UserAddOutlined,
        CalendarFilled,
        BankFilled,
        CheckOutlined,
        UsergroupAddOutlined,
        UploadOutlined,
        FileOutlined,
        FolderOpenOutlined,
        AccountBookOutlined,
        GroupOutlined,
        UserOutlined,
        SettingOutlined,
        LogoutOutlined,
        CarryOutOutlined,
        BankOutlined,
        BranchesOutlined,
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
            selected: this.$page.props.company
                ? this.$page.props.company.name
                : "",
            selectedyear: this.$page.props.year
                ? this.$page.props.year.end
                : "",
            yr_id: this.$page.props.year,
            years: this.$page.props.years,
        };
    },

    methods: {
        coch(value) {
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
.ant-layout-sider-children {
    height: 100vh;
    overflow: auto;
    margin-top: -0.1px;
    padding-top: 0.1px;
}

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

.ant-table.ant-table-small .ant-table-title,
.ant-table.ant-table-small .ant-table-footer,
.ant-table.ant-table-small .ant-table-thead>tr>th,
.ant-table.ant-table-small .ant-table-tbody>tr>td,
.ant-table.ant-table-small tfoot>tr>th,
.ant-table.ant-table-small tfoot>tr>td {
    padding: 6px 8px !important;
}
</style>
