@extends('server.layouts.app')
@section('title')
    Sites
@endsection

@section('content')
    <div class="flex flex-col py-8">
        <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-300 shadow-sm !pt-2 px-2">
            <div class="flex items-center justify-between w-full pb-2">
                <h2 class="font-medium text-gray-800 dark:text-white text-base font-headline mb-4 mb-0 pl-2 !mb-0">
                    List
                </h2>

                <div class="flex items-center space-x-2 justify-end">
                    <label for="showToken"
                           class="text-gray-600 dark:text-gray-100 text-sm dark:text-gray-100">
                        Show token
                    </label>

                    <div class="inline-flex items-center">
                        <label class="flex items-center cursor-pointer relative">
                            <input id="showToken"
                                   v-model="showToken"
                                   name="showToken"
                                   value="1"
                                   type="checkbox"
                                   class="peer h-5 w-5 cursor-pointer transition-all appearance-none rounded border bg-white dark:bg-gray-700 shadow-sm border-gray-200 checked:border-transparent dark:border-gray-700 checked:bg-primary"
                            />
                            <span class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
                                                    @include('icons.checkmark')
                                    </span>
                        </label>
                    </div>

                </div>
            </div>

            <div class="rounded-lg bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-white/10 shadow-md !-mb-2">
                <div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-fixed divide-y divide-gray-200 dark:divide-white/20 text-gray-800 whitespace-nowrap whitespace-normal ">
                            <thead>
                            <tr>
                                <th class="p-4 text-left text-sm font-medium text-gray-500 dark:text-white">
                                    Host
                                </th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500 dark:text-white">
                                    Subdomain
                                </th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500 dark:text-white">
                                    <span v-if="showToken">
                                        Token
                                    </span>
                                    <span v-else>
                                        User
                                    </span>
                                </th>
                                <th class="p-4 text-sm font-medium text-gray-500 dark:text-white text-right">
                                    Shared At
                                </th>
                                <th class="py-4 px-6 text-left text-sm font-medium text-gray-500 dark:text-white">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/20 text-sm">
                            <tr v-if="sites.length > 0" v-for="site in sites">
                                <td class="px-4 py-3 font-mono text-gray-800 dark:text-gray-300">
                                    @{ site.host }
                                </td>
                                <td class="px-4 py-3 font-mono text-gray-800 dark:text-gray-300">
                                    <span v-if="site.server_host">@{ site.subdomain }.@{ site.server_host }</span>
                                    <span v-else>@{ site.subdomain }.{{ $configuration->hostname()}}:{{ $configuration->port() }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-300">
                                    <span v-if="showToken" class="font-mono">
                                        @{ site.user?.auth_token }
                                    </span>
                                    <span v-else>
                                        @{ site.user?.name }
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-800 text-right dark:text-gray-300">
                                    @{ site.shared_at }
                                </td>
                                <td class="px-4 py-3 text-right text-gray-800 dark:text-gray-300">
                                    <button @click.prevent="visit('{!! $scheme !!}://'+site.subdomain+'.'+(site.server_host || '{{ $configuration->hostname()}}:{{ $configuration->port() }}'))" type="button"
                                            title="Visit site"
                                            class="relative items-center font-medium justify-center gap-2 whitespace-nowrap group disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg w-10 inline-flex bg-transparent dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/15 text-gray-400 hover:text-gray-800 dark:text-white dark:hover:text-white">
                                        @include('icons.micro-globe')
                                    </button>
                                    <button @click.prevent="disconnectSite(site.client_id)" type="button"
                                            title="Disconnect site"
                                            class="relative items-center font-medium justify-center gap-2 whitespace-nowrap group disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg w-10 inline-flex bg-transparent dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/15 text-red-600 hover:text-red-600 dark:text-white">
                                        @include('icons.micro-stop-circle')
                                    </button>
                                </td>
                            </tr>


                            <tr v-if="sites.length <= 0 "
                                class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 text-xs text-center text-gray-700 dark:text-gray-300" colspan="6">
                                    No sites shared yet.
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        new Vue({
            el: '#app',

            delimiters: ['@{', '}'],

            data: {
                sites: [],
                showToken: false
            },

            methods: {
                getSites() {
                    fetch('/api/sites')
                        .then((response) => {
                            return response.json();
                        }).then((data) => {
                        this.sites = data.sites;
                    });
                },
                disconnectSite(id) {
                    fetch('/api/sites/' + id, {
                        method: 'DELETE',
                    }).then((response) => {
                        return response.json();
                    }).then((data) => {
                        this.sites = data.sites;
                    });
                },
                visit(url) {
                    window.open(url, '_blank');
                }
            },

            mounted() {
                this.getSites();
            }
        })
    </script>
@endsection
