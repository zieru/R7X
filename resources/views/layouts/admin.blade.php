<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">

    <!-- admin.css here -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    <!-- app js values -->
    <script type="application/javascript">
        var LSK_APP = {};
        LSK_APP.APP_URL = 'http://localhost:8000';
        console.log(LSK_APP);
    </script>
</head>
<body>
<div id="admin">

    <template>

        <v-app id="inspire">
            <v-navigation-drawer
                    :clipped="$vuetify.breakpoint.lgAndUp"

                    color="gray lighten-4"
                    v-model="drawer"
                    elevation="24"
                    dense
                    app
                    left>
                <v-list dense>
                    <v-list-item class="px-2">
                        <v-list-item-avatar>
                            <v-icon>mdi-account</v-icon>
                        </v-list-item-avatar>
                    </v-list-item>

                    <v-list-item link>
                        <v-list-item-content>
                            <v-list-item-title class="title">{{ Auth::user()->name }}</v-list-item-title>
                            <v-list-item-subtitle>{{ Auth::user()->email }}</v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item-group>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>
                                   Navigation
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-divider></v-divider>
                        @foreach($nav as $n)
                            @if($n->navType==\App\Components\Core\Menu\MenuItem::$NAV_TYPE_NAV && $n->visible)
                                <v-list-item :to="{name: '{{$n->routeName}}'}" :exact="false" active-class="primary--text">
                                    <v-list-item-icon>
                                        <v-icon>{{$n->icon}}</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{$n->label}}
                                        </v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            @else
                                <v-divider></v-divider>
                            @endif
                        @endforeach


                        <v-list-item @click="clickLogout('{{route('logout')}}','{{url('/')}}')">
                            <v-list-item-action>
                                <v-icon>directions_walk</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title>Logout</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                    </v-list-item-group>
                </v-list>
            </v-navigation-drawer>


            <v-app-bar :clipped-left="$vuetify.breakpoint.lgAndUp" color="#272C33" dark
                       app elevate-on-scroll hide-on-scroll>
                <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
                <v-toolbar-title class="ml-0 pl-4">
                    <v-avatar tile><v-img class="logo" src="{{url('img/logo.png')}}" style="filter: invert(42%) sepia(93%) saturate(1352%) hue-rotate(87deg) brightness(119%) contrast(0%);; padding: 8px;"> </v-avatar>
                    <span class="title ml-3 mr-5">{{config('app.name')}}</span>
                </v-toolbar-title>

                <v-spacer></v-spacer>
                {{--<v-btn icon>
                    <v-icon>mdi-apps</v-icon>
                </v-btn>
                <v-btn icon>
                    <v-icon>mdi-bell</v-icon>
                </v-btn>
                <v-btn
                        icon
                        large
                >
                    <v-avatar
                            size="32px"
                            item
                    ><v-icon>mdi-account</v-icon>
                       </v-avatar>
                </v-btn>--}}
                <v-avatar tile> <v-img src="http://172.28.136.242/myportal/assets/logo.svg" alt="homepage" class="logo" style="filter: invert(42%) sepia(93%) saturate(1352%) hue-rotate(87deg) brightness(119%) contrast(0%);; padding: 8px;"></v-img></v-avatar>
            </v-app-bar>

            <v-content style="background: #F5F5F5">
                <div>
                    <v-breadcrumbs :items="getBreadcrumbs" divider=">" large>
                        <template v-slot:item="props">
                            <v-breadcrumbs-item :to="props.item.to" exact
                                                :key="props.item.label"
                                                :disabled="props.item.disabled">
                                <template v-slot:divider>
                                    <v-icon>mdi-forward</v-icon>
                                </template>
                                @{{ props.item.label }}
                            </v-breadcrumbs-item>
                        </template>
                    </v-breadcrumbs>
                </div>
                <v-divider></v-divider>
                <transition name="fade">
                    <router-view ></router-view>
                </transition>


            </v-content>
            <v-footer
                    absolute
                    class="justify-center"
                    {{--color="white"--}}
                    inset app
            >
                <v-row
                        justify="center"
                        no-gutters
                >
                    {{--<v-btn
                            v-for="link in links"
                            :key="link"
                            color="white"
                            text
                            rounded
                            class="my-2"
                    >
                    </v-btn>--}}
                    <v-col
                            class="py-4"
                            cols="6"

                    >
                        <span style="color:#757575;">
                            Copyright {{ date('Y') }} — <strong>{{ config('app.name', 'Laravel') }}</strong>
                        </span>

                    </v-col>
                    <v-col
                            class="text-right"
                            class="py-4"
                            cols="6"
                    >
                        <small>Laravel {{ App::VERSION() }}</small>
                    </v-col>
                </v-row>
            </v-footer>
        </v-app>

        <!-- loader -->
        <div v-if="showLoader" class="wask_loader bg_half_transparent">
            <fade-loader color="red"></fade-loader>
        </div>

        <!-- snackbar -->
        <v-snackbar
                :timeout="snackbarDuration"
                :color="snackbarColor"
                top
                v-model="showSnackbar">
            @{{ snackbarMessage }}
        </v-snackbar>

        <!-- dialog confirm -->
        <v-dialog v-show="showDialog" v-model="showDialog" absolute max-width="450px">
            <v-card>
                <v-card-title>
                    <div class="headline"><v-icon v-if="dialogIcon">@{{dialogIcon}}</v-icon> @{{ dialogTitle }}</div>
                </v-card-title>
                <v-card-text>@{{ dialogMessage }}</v-card-text>
                <v-card-actions v-if="dialogType=='confirm'">
                    <v-spacer></v-spacer>
                    <v-btn color="orange darken-1" text @click.native="dialogCancel">Cancel</v-btn>
                    <v-btn color="green darken-1" text @click.native="dialogOk">Ok</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- the progress bar -->
        <vue-progress-bar></vue-progress-bar>

    </template>

</div>

<script src="{{ asset('js/manifest.js') }}"></script>
<script src="{{ asset('js/vendor.js') }}"></script>
<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>