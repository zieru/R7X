<template>
    <div>


        <v-data-iterator
      :items="items"
      :loading="loading"
      :items-per-page.sync="itemsPerPage"
      :options.sync="pagination"
      :server-items-length="totalItems"
      :page="page"
      :search="search"
      :sort-by="sortBy.toLowerCase()"
      :sort-desc="sortDesc"

    >
<template v-slot:header>
        <v-toolbar
          dark
          color="primary darken-1"
          class="mb-1"
        >
          <v-text-field
            v-model="search"
            clearable
            flat
            solo-inverted
            hide-details
            prepend-inner-icon="search"
            label="Search"
          ></v-text-field>
          <template v-if="$vuetify.breakpoint.mdAndUp">
            <v-spacer></v-spacer>
            <v-select
              v-model="sortBy"
              flat
              solo-inverted
              hide-details
              prepend-inner-icon="sort"
              label="Sort by"
            ></v-select>
            <v-spacer></v-spacer>
            <v-btn-toggle
              v-model="sortDesc"
              mandatory
            >
              <v-btn
                depressed
                color="primary"
                :value="false"
              >
                <v-icon>mdi-arrow-up</v-icon>
              </v-btn>
              <v-btn
                depressed
                color="primary"
                :value="true"
              >
                <v-icon>mdi-arrow-down</v-icon>
              </v-btn>
            </v-btn-toggle>
          </template>
        </v-toolbar>
      </template>

      <template v-slot:default="props" v-cloak>
        <v-row>
          <v-col
            v-for="item in props.items"
            :key="item.isi"
            cols="12"
            sm="12"
            md="12"
            lg="12"
            v-cloak
          >
            <v-card >
             <v-list-item>
                <v-list-item-avatar color="grey">
                  <v-icon>mdi-account</v-icon>                
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title>
                    <UserProfileTitleCard :userdata="item.user"> </UserProfileTitleCard>
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    
                    <span v-if="$appFormatters.formatDatetimeago(item.created_at) == 'Invalid date'">{{ item.updated_at }}</span>
                    <v-spacer class="float-right">{{ item.ket }}</v-spacer>
                    
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
                <v-divider></v-divider>
                  <v-card-text>
                      <span v-if="item.isi">{{ item.isi }}</span>
                      <span v-else="item.isi"><i>tidak ada isi pesan</i></span>
                  </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </template>

    </v-data-iterator>
        <v-expansion-panels accordion>
            <v-expansion-panel
            >
                <v-expansion-panel-header><v-icon v-text="" style="flex:none">mdi-reply</v-icon> Reply / Balas</v-expansion-panel-header>
                <v-expansion-panel-content>
                    <Editor>

                    </Editor>

                </v-expansion-panel-content>
            </v-expansion-panel>
        </v-expansion-panels>
    </div>
</template>
<script>
export default {
    name:'LogView',
    beforeMount () {
        this.childData = this.parentData // save props data to itself's data and deal with it
    },
    data () {
      return {
        loading: true,
        totalItems: 0,
        search: '',
        pagination: {},
        childData: '',
        LogCaseData: null,
        page: 1,
        itemsPerPage: 2,
        itemsPerPageArray: [4, 8, 12],
        sortDesc: false,
        sortBy: 'created_at',

      items: [],
      }
    },
    computed: {
      numberOfPages () {
          console.log(this.loadLogCase);
        //return Math.ceil(this.LogCaseData.length / this.itemsPerPage)
      },
      filteredKeys () {
        return this.keys.filter(key => key !== `isi`)
      },
    },
    props:{
        parentData: {
            type: String,
            default () {
            return ''
            }
        }
    },
    watch:{
      pagination: {
                handler () {
                    this.loadLogCase(()=>{});
                },
                deep: true,
            }
    },
    methods: {
      loadLogCase(cb) {
                const self = this;
                self.loading = true;

                let filter_status = null;
                if (self.filter_status) filter_status = self.filter_status;

                const direction = self.pagination.sortDesc[0] ? 'desc' : 'asc';
                let params = {
                    "search[value]": self.search,
                    status: filter_status,
                    draw: self.pagination.page,
                    length: self.pagination.itemsPerPage,
                    start: (((self.pagination.page/self.pagination.itemsPerPage)*self.pagination.itemsPerPage)-1)*self.pagination.itemsPerPage,
                    /*'order[0][dir]': direction,*/
                };

                $.each(self.headers, function(key, value) {
                    params['columns['+key+'][data]'] = value.value;
                    params['columns['+key+'][searchable]'] = value.filterable;
                    params['columns['+key+'][orderable]'] = value.sortable;
                });

                axios.get('/api/admin/caserecording/'+self.childData+'/log',{params: params})
                    .then(function(response) {
                        self.items = response.data.data;
                        self.totalItems = response.data.recordsFiltered;
                        self.pagination.totalItems = self.totalItems;
                        (cb || Function)();

                    }).catch(function(error) {
                        console.log(error.response);
                    if (error.response && error.response.status != 200) {
                        self.$store.commit('showSnackbar', {
                            message: error.response.status+':'+ error.response.statusText + ' Please Reload Your Browser',
                            color: 'error',
                            duration: 10000
                        });
                    };
                })
                  self.loading = false;
                  self.tableloading = false;
            },
      nextPage () {
        if (this.page + 1 <= this.numberOfPages) this.page += 1
      },
      formerPage () {
        if (this.page - 1 >= 1) this.page -= 1
      },
      updateItemsPerPage (number) {
        this.itemsPerPage = number
      },
    },
    mounted(){
        const self = this;
        self.loadLogCase();
    }
}
</script>