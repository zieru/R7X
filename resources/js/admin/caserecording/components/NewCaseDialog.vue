<template>

    <v-card>
        <v-form v-model="valid" ref="NewCaseForm" @keyup.native.enter="save()">
            <v-alert
                    icon="mdi-shield-lock-outline"
                    prominent
                    dense
                    text
                    type="info"
            >
                Agar Case dapat cepat ditanggapi, Mohon untuk dapat mengisi data dengan benar dan seakurat mungkin.
            </v-alert>
            <v-container>
                <v-row>
                    <v-col
                            cols="12"
                            md="10"
                    >
                    <v-text-field
                            :rules="nameRules"
                            :counter="64"
                            v-model="vsubject"
                            label="Subject / Judul"
                            prepend-icon="subject"
                            outlined
                            rounded
                            required
                    ></v-text-field>
                    </v-col>
                    <v-col
                            cols="12"
                            md="2"
                    >
                        <v-checkbox
                                label="Fast Priority"
                                color="primary"
                                hint="Centang Jika butuh cepat"
                                v-model="vpriority"
                        ></v-checkbox>
                    </v-col>
                </v-row>
                <v-divider></v-divider>
                <v-row>
                    <v-col
                            cols="12"
                            md="4"
                    >


                        <v-select
                                :items="itemtypecases"
                                label="Call Direction"
                                v-model="vcalldirection"
                                :rules="calldirectionRules"
                                required
                        ></v-select>

                    </v-col>

                    <v-col
                            cols="12"
                            md="4"
                    >
                        <v-text-field
                                prepend-icon="phone"
                                v-model="vmsisdncaller"
                                :counter="16"
                                label="MSISDN Menghubungi (Caller)"
                                required
                        ></v-text-field>
                    </v-col>

                    <v-col
                            cols="12"
                            md="4"
                    >
                        <v-text-field
                                :rules="nameRules"
                                v-model="vmsisdnprob"
                                prepend-icon="phone"
                                :counter="16"
                                label="MSISDN Bermasalah"
                        ></v-text-field>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col
                            cols="12"
                            md="6"
                    >
                        <v-text-field
                                :counter="12"
                                type="number"
                                v-model="vagentid"
                                label="NIK Agent"
                                prepend-inner-icon="person"
                                hint="Usahakan diisi, dikarenakan terkadang terdapat lebih dari 1 agent"
                        ></v-text-field>
                    </v-col>
                    <v-col
                            cols="12"
                            md="6"
                    >
                        <v-col cols="12" sm="12" md="12">
                            <v-dialog
                                    ref="dialog"
                                    v-model="modaldatekejadian"
                                    :return-value.sync="dates"
                                    persistent
                                    width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                            v-model="dates"
                                            label="Tanggal Kejadian"
                                            prepend-icon="event"
                                            readonly
                                            v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker v-model="dates" range scrollable>
                                    <v-spacer></v-spacer>
                                    <v-btn text color="primary" @click="modaldatekejadian = false">Cancel</v-btn>
                                    <v-btn text color="primary" @click="$refs.dialog.save(dates)">OK</v-btn>
                                </v-date-picker>
                            </v-dialog>
                        </v-col>
                    </v-col>
                </v-row>
            </v-container>
            <v-divider></v-divider>
            <v-container>
                <v-textarea
                        counter
                        outlined
                        rounded
                        label="Isi / Remark"
                        required
                        :rules="descriptionRules"
                        v-model="vremark"
                        :counter="65000"
                        prepend-icon="view_headline"
                ></v-textarea>
            </v-container>
        </v-form>


        <v-btn @click="save()" :disabled="!valid" color="primary">Save</v-btn>
    </v-card>
</template>

<script>
    export default {
        components: {
        },
        props:{
            /*mask: {
                type: String,
                default: '#### — #### — #### — ####'
            },*/
        },
        name: "NewCaseDialog",
        data: () => ({
            valid: false,
            isLoading: false,
            vsubject: "",
            vpriority: false,
            vcalldirection:"",
            vmsisdncaller:"",
            vmsisdnprob:"",
            vagentid:"",
            vremark:"",
            nameRules: [
                (v) => !!v || 'Subject is required',
            ],
            calldirectionRules: [
                (v) => !!v || 'Choose one Call Direction',
            ],
            descriptionRules: [
                (v) => !!v || 'Remark is required',
            ],
            modaldatekejadian: false,
            itemtypecases: ['IBC CALL-IN','IBC CALL-OUT (CallBack)','OBC CALL-OUT'],
            dates: [new Date().toISOString().substr(0, 10), new Date().toISOString().substr(0, 10)],
        }),
        methods:{
            save() {
                const self = this;

                let payload = {
                    subject: self.vsubject,
                    remark: self.vremark,
                    priority: self.vpriority,
                    calldirection: self.vcalldirection,
                    agentid:    self.vagentid,
                    dates:  self.dates,
                    msisdncaller: self.vmsisdncaller,
                    msisdnprob: self.vmsisdnprob,
                };
                console.log(payload);
                self.isLoading = true;

                axios.post('/api/admin/caserecording/createnew',payload).then(function(response) {

                    self.$store.commit('showSnackbar',{
                        message: response.data.message,
                        color: 'success',
                        duration: 3000
                    });

                    self.isLoading = false;
                    self.$eventBus.$emit('FILE_GROUP_ADDED');

                    // reset
                    //self.$refs.NewCaseForm.reset();

                }).catch(function (error) {
                    self.isLoading = false;
                    if (error.response) {
                        self.$store.commit('showSnackbar',{
                            message: error.response.data.message,
                            color: 'error',
                            duration: 3000
                        });
                    } else if (error.request) {
                        console.log(error.request);
                    } else {
                        console.log('Error', error.message);
                    }
                });
            }
        }


    }
</script>

<style scoped>
    .required label::after {
        content: "*";
    }
</style>