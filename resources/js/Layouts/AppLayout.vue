<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import NavLink from '@/Components/NavLink.vue';
import { Toaster } from 'vue-sonner'
import 'vue-sonner/style.css'

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />
        <Toaster richColors position="bottom-right" theme="light" :closeButton="true" closeButtonPosition="top-right" />

        <div class="min-h-screen bg-base-300">
<!--            <nav class="bg-white border-b border-gray-100">-->
<!--                &lt;!&ndash; Primary Navigation Menu &ndash;&gt;-->
<!--                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">-->
<!--                    <div class="flex justify-between h-16">-->
<!--                        <div class="flex">-->
<!--                            &lt;!&ndash; Logo &ndash;&gt;-->
<!--                            <div class="shrink-0 flex items-center">-->
<!--                                <Link :href="route('dashboard')">-->
<!--                                    <ApplicationMark class="block h-9 w-auto" />-->
<!--                                </Link>-->
<!--                            </div>-->

<!--                            &lt;!&ndash; Navigation Links &ndash;&gt;-->
<!--                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">-->
<!--                                <NavLink :href="route('livros.index')" :active="route().current('livros.index')">-->
<!--                                    Livros-->
<!--                                </NavLink>-->

<!--                                <NavLink :href="route('autores.index')" :active="route().current('autores.index')">-->
<!--                                    Autores-->
<!--                                </NavLink>-->

<!--                                <NavLink :href="route('editoras.index')" :active="route().current('editoras.index')">-->
<!--                                    Editoras-->
<!--                                </NavLink>-->
<!--                            </div>-->
<!--                        </div>-->

<!--                        <div class="hidden sm:flex sm:items-center sm:ms-6">-->
<!--                            <div class="ms-3 relative">-->
<!--                                &lt;!&ndash; Teams Dropdown &ndash;&gt;-->
<!--                                <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">-->
<!--                                    <template #trigger>-->
<!--                                        <span class="inline-flex rounded-md">-->
<!--                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">-->
<!--                                                {{ $page.props.auth.user.current_team.name }}-->

<!--                                                <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">-->
<!--                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />-->
<!--                                                </svg>-->
<!--                                            </button>-->
<!--                                        </span>-->
<!--                                    </template>-->

<!--                                    <template #content>-->
<!--                                        <div class="w-60">-->
<!--                                            &lt;!&ndash; Team Management &ndash;&gt;-->
<!--                                            <div class="block px-4 py-2 text-xs text-gray-400">-->
<!--                                                Manage Team-->
<!--                                            </div>-->

<!--                                            &lt;!&ndash; Team Settings &ndash;&gt;-->
<!--                                            <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">-->
<!--                                                Team Settings-->
<!--                                            </DropdownLink>-->

<!--                                            <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">-->
<!--                                                Create New Team-->
<!--                                            </DropdownLink>-->

<!--                                            &lt;!&ndash; Team Switcher &ndash;&gt;-->
<!--                                            <template v-if="$page.props.auth.user.all_teams.length > 1">-->
<!--                                                <div class="border-t border-gray-200" />-->

<!--                                                <div class="block px-4 py-2 text-xs text-gray-400">-->
<!--                                                    Switch Teams-->
<!--                                                </div>-->

<!--                                                <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">-->
<!--                                                    <form @submit.prevent="switchToTeam(team)">-->
<!--                                                        <DropdownLink as="button">-->
<!--                                                            <div class="flex items-center">-->
<!--                                                                <svg v-if="team.id == $page.props.auth.user.current_team_id" class="me-2 size-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">-->
<!--                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />-->
<!--                                                                </svg>-->

<!--                                                                <div>{{ team.name }}</div>-->
<!--                                                            </div>-->
<!--                                                        </DropdownLink>-->
<!--                                                    </form>-->
<!--                                                </template>-->
<!--                                            </template>-->
<!--                                        </div>-->
<!--                                    </template>-->
<!--                                </Dropdown>-->
<!--                            </div>-->

<!--                            &lt;!&ndash; Settings Dropdown &ndash;&gt;-->
<!--                            <div class="ms-3 relative">-->
<!--                                <Dropdown align="right" width="48">-->
<!--                                    <template #trigger>-->
<!--                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">-->
<!--                                            <img class="size-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">-->
<!--                                        </button>-->

<!--                                        <span v-else class="inline-flex rounded-md">-->
<!--                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">-->
<!--                                                {{ $page.props.auth.user.name }}-->

<!--                                                <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">-->
<!--                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />-->
<!--                                                </svg>-->
<!--                                            </button>-->
<!--                                        </span>-->
<!--                                    </template>-->

<!--                                    <template #content>-->
<!--                                        &lt;!&ndash; Account Management &ndash;&gt;-->
<!--                                        <div class="block px-4 py-2 text-xs text-gray-400">-->
<!--                                            Manage Account-->
<!--                                        </div>-->

<!--                                        <DropdownLink :href="route('profile.show')">-->
<!--                                            Profile-->
<!--                                        </DropdownLink>-->

<!--                                        <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">-->
<!--                                            API Tokens-->
<!--                                        </DropdownLink>-->

<!--                                        <div class="border-t border-gray-200" />-->

<!--                                        &lt;!&ndash; Authentication &ndash;&gt;-->
<!--                                        <form @submit.prevent="logout">-->
<!--                                            <DropdownLink as="button">-->
<!--                                                Log Out-->
<!--                                            </DropdownLink>-->
<!--                                        </form>-->
<!--                                    </template>-->
<!--                                </Dropdown>-->
<!--                            </div>-->
<!--                        </div>-->

<!--                        &lt;!&ndash; Hamburger &ndash;&gt;-->
<!--                        <div class="-me-2 flex items-center sm:hidden">-->
<!--                            <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" @click="showingNavigationDropdown = ! showingNavigationDropdown">-->
<!--                                <svg-->
<!--                                    class="size-6"-->
<!--                                    stroke="currentColor"-->
<!--                                    fill="none"-->
<!--                                    viewBox="0 0 24 24"-->
<!--                                >-->
<!--                                    <path-->
<!--                                        :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"-->
<!--                                        stroke-linecap="round"-->
<!--                                        stroke-linejoin="round"-->
<!--                                        stroke-width="2"-->
<!--                                        d="M4 6h16M4 12h16M4 18h16"-->
<!--                                    />-->
<!--                                    <path-->
<!--                                        :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"-->
<!--                                        stroke-linecap="round"-->
<!--                                        stroke-linejoin="round"-->
<!--                                        stroke-width="2"-->
<!--                                        d="M6 18L18 6M6 6l12 12"-->
<!--                                    />-->
<!--                                </svg>-->
<!--                            </button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->

<!--                &lt;!&ndash; Responsive Navigation Menu &ndash;&gt;-->
<!--                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">-->
<!--                    <div class="pt-2 pb-3 space-y-1">-->
<!--                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">-->
<!--                            Dashboard-->
<!--                        </ResponsiveNavLink>-->
<!--                    </div>-->

<!--                    &lt;!&ndash; Responsive Settings Options &ndash;&gt;-->
<!--                    <div class="pt-4 pb-1 border-t border-gray-200">-->
<!--                        <div class="flex items-center px-4">-->
<!--                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">-->
<!--                                <img class="size-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">-->
<!--                            </div>-->

<!--                            <div>-->
<!--                                <div class="font-medium text-base text-gray-800">-->
<!--                                    {{ $page.props.auth.user.name }}-->
<!--                                </div>-->
<!--                                <div class="font-medium text-sm text-gray-500">-->
<!--                                    {{ $page.props.auth.user.email }}-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->

<!--                        <div class="mt-3 space-y-1">-->
<!--                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">-->
<!--                                Profile-->
<!--                            </ResponsiveNavLink>-->

<!--                            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">-->
<!--                                API Tokens-->
<!--                            </ResponsiveNavLink>-->

<!--                            &lt;!&ndash; Authentication &ndash;&gt;-->
<!--                            <form method="POST" @submit.prevent="logout">-->
<!--                                <ResponsiveNavLink as="button">-->
<!--                                    Log Out-->
<!--                                </ResponsiveNavLink>-->
<!--                            </form>-->

<!--                            &lt;!&ndash; Team Management &ndash;&gt;-->
<!--                            <template v-if="$page.props.jetstream.hasTeamFeatures">-->
<!--                                <div class="border-t border-gray-200" />-->

<!--                                <div class="block px-4 py-2 text-xs text-gray-400">-->
<!--                                    Manage Team-->
<!--                                </div>-->

<!--                                &lt;!&ndash; Team Settings &ndash;&gt;-->
<!--                                <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)" :active="route().current('teams.show')">-->
<!--                                    Team Settings-->
<!--                                </ResponsiveNavLink>-->

<!--                                <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')" :active="route().current('teams.create')">-->
<!--                                    Create New Team-->
<!--                                </ResponsiveNavLink>-->

<!--                                &lt;!&ndash; Team Switcher &ndash;&gt;-->
<!--                                <template v-if="$page.props.auth.user.all_teams.length > 1">-->
<!--                                    <div class="border-t border-gray-200" />-->

<!--                                    <div class="block px-4 py-2 text-xs text-gray-400">-->
<!--                                        Switch Teams-->
<!--                                    </div>-->

<!--                                    <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">-->
<!--                                        <form @submit.prevent="switchToTeam(team)">-->
<!--                                            <ResponsiveNavLink as="button">-->
<!--                                                <div class="flex items-center">-->
<!--                                                    <svg v-if="team.id == $page.props.auth.user.current_team_id" class="me-2 size-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">-->
<!--                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />-->
<!--                                                    </svg>-->
<!--                                                    <div>{{ team.name }}</div>-->
<!--                                                </div>-->
<!--                                            </ResponsiveNavLink>-->
<!--                                        </form>-->
<!--                                    </template>-->
<!--                                </template>-->
<!--                            </template>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </nav>-->

            <div class="drawer lg:drawer-open">
                <input id="sidebar" type="checkbox" class="drawer-toggle" />
                <div class="drawer-content">
                    <header v-if="$slots.header" class="glass sticky top-4 z-50 mx-auto w-[95%] max-w-6xl rounded-3xl border border-[#DCC1B1]/30 shadow-sm">
                        <div class="container gap-4 py-6 px-6 md:px-8 max-w-7xl mx-auto">
                            <slot name="header" />
                        </div>
                    </header>
                    <!-- Page content here -->
                    <main class="p-4 mt-4"> <slot/> </main>
                </div>

                <div class="drawer-side is-drawer-close:overflow-visible">
                    <div class="flex min-h-full flex-col items-start bg-base-200 is-drawer-close:w-14 is-drawer-open:w-64 glass">
                        <!-- Sidebar content here -->
                        <ul class="menu w-full grow">
                            <!-- List item -->
                            <li>
                                <label for="sidebar" aria-label="open sidebar" class="btn btn-square btn-ghost">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        class="inline-block h-6 w-6 stroke-current"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"
                                        ></path>
                                    </svg>
                                </label>
                            </li>
                            <li>
                                <button class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Homepage">
                                    <!-- Home icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round" stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor" class="my-1.5 inline-block size-4"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                                    <span class="is-drawer-close:hidden">Homepage</span>
                                </button>
                            </li>

                            <li>
                                <NavLink :href="route('livros.index')" :active="route().current('livros.index')">
                                    Livros
                                </NavLink>
                            </li>
                            <li>
                                <NavLink :href="route('autores.index')" :active="route().current('autores.index')">
                                    Autores
                                </NavLink>
                            </li>
                            <li>
                                <NavLink :href="route('editoras.index')" :active="route().current('editoras.index')">
                                    Editoras
                                </NavLink>
                            </li>

                            <!-- List item -->
                            <li>
                                <button class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Settings">
                                    <!-- Settings icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round" stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor" class="my-1.5 inline-block size-4"><path d="M20 7h-9"></path><path d="M14 17H5"></path><circle cx="17" cy="17" r="3"></circle><circle cx="7" cy="7" r="3"></circle></svg>
                                    <span class="is-drawer-close:hidden">Settings</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
