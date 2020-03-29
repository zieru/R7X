<template>
    <div class="page_wrap_vue pa-3">
        <h2>Dashboard</h2>
        <v-row no-gutters>
            <v-col
                    v-for="n in 3"
                    :key="n"
                    cols="12"
                    sm="4"
            >
                <v-card
                        class="pa-2"
                        outlined
                        tile
                >
                    <comingsoonblock/>
                </v-card>
            </v-col>
        </v-row>

    </div>

</template>

<script>
    import ComingSoon from './../ComingSoon';
    export default {

        components: {
            'comingsoonblock':ComingSoon,
        },
        mounted() {
            const self = this;

            self.$store.commit('setBreadcrumbs',[
                {label:'Dashboard',name:''}
            ]);
        }
    }
</script>