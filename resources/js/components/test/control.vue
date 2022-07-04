<template>
    <div class="p-0">
        <div class="card">
            <div class="card-header ui-sortable-handle" style="cursor: move;">
                <h3 class="card-title">
                    <i class="fa fa-laptop-house mr-1"></i>
                    Control
                </h3>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item mr-1">
                            <!-- <button class="btn btn-sm btn-primary" @click="createMode"><i class="fas fa-plus-circle"></i> Add New</button> -->
                        </li>
                        <li class="nav-item">
                            <div class="input-group mt-0 input-group-sm" style="width: 350px;" v-if="tests">
                                <input type="text" name="table_search" v-model="searchWord"  class="form-control float-right" placeholder="Search by Customer Code">

                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default" @click="search"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div><!-- /.card-header -->
            <div class="card-body table-responsive p-0" >
                <table class="table">
                    <thead>

                        <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Patient</th>
                        <th>TestRequested</th>
                        <th>Test Type</th>
                        <th>Doctors Ref</th>
                        <th>Batch</th>
                        <th>Action</th>
                        <th>Date Created</th>
                        </tr>

                    </thead>
                    <tbody>
                        <tr v-for="test in tests.data" :key="test.id">
                            <td>{{ test.id }}</td>
                            <td>{{ test.patient.customerId }}</td>
                            <td>{{ test.patient.first_name }}</td>
                            <td>{{ test.requested_test }}</td>
                            <td>{{ test.price.test }}</td>
                            <td>{{ test.doctors_ref }}</td>
                            <td>{{ test.batch }}</td>
                            <td>
                                <button class="btn btn-sm btn-info" @click="viewTest(test)"> <i class="fa fa-eye"></i> View</button>
                                <button class="btn btn-sm btn-warning" @click="editTest(test)" > <i class="fa fa-edit"></i> Control</button>
                                <!-- <button class="btn btn-sm btn-danger" @click="deleteTest(test)"> <i class="fa fa-trash"></i> Delete </button> -->
                                <!-- <button class="btn btn-sm btn-info" @click="deleteTest(test)"> <i class="fa fa-print"></i> GenerateReciept </button> -->
                            </td>
                            <td>
                                {{ test.created_at | date }}
                            </td>
                        </tr>
                        <tr v-if="tests && ! isSearch">
                            <td colspan="8">
                                <!-- http://127.0.0.1:8000/getAllSamples-->
                                <select v-model="per_page" @change="gotoPage(per_page)">
                                    <option v-for="page in pages" :value="page.value">{{page.text}}</option>
                                </select>
                                <!-- <span>{{}}</span> -->
                                <button :disabled="!tests.prev_page_url" class="btn btn-sm btn-info" @click="gotoURL(tests.prev_page_url)"> <i class="fa fa-eye"></i> Prev</button>
                                <button :disabled="!tests.next_page_url" class="btn btn-sm btn-info" @click="gotoURL(tests.next_page_url)"> <i class="fa fa-eye"></i> Next</button>
                            </td>
                        </tr>

                        <tr v-if="tests">
                            <td colspan="8">
                                <button :disabled="!isSearch" class="btn btn-sm btn-info" @click="resetTable()"> <i class="fa fa-eye"></i> Reset</button>
                            </td>
                        </tr>
                    </tbody>
                </table>


                <div>
                    <span>
                       <!-- test-->
                    </span>
                </div>

            </div>

            <loading :loading="loading"></loading>
        </div>
<!-- View Modal -->
        <div class="modal fade" id="viewTest" tabindex="-1" role="dialog" aria-labelledby="viewTestModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row" >
                            <div class="col-md-6" v-if="test">
                                <p><b>Patient:</b> {{ test.patient.first_name }}</p>
                                <p><b>Price:</b> {{ test.price.amount }}</p>
                                <p><b>Test Requested:</b> {{ test.requested_test }}</p>
                                <p><b>Payment Mode:</b> {{ test.payment_mode }}</p>
                                <p><b>Test Type:</b> {{ test.price.test }}</p>
                                <p><b>Last Updated:</b> {{ test.updated_at | date }}</p>
                                <p><b>Date Posted:</b> {{ test.created_at | date }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Create Modal -->
        <div class="modal fade" id="createTest" tabindex="-1" role="dialog" aria-labelledby="createTestModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createTestModalLabel" v-show="!editMode">Control</h5>
                        <h5 class="modal-title" id="createTestModalLabel" v-show="editMode">Control</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="formSubmit()" enctype="multipart/form-data">
                        <div class="modal-body">

                            <div class="form-group">
                                <label>Patient Code</label>
                                <select class="form-control" v-model="form.patient_id" :disabled="isDisabled">
                                    <option value="0">Select Patient</option>
                                    <option v-for="patient in patients" :value="patient.id">{{ patient.customerId}}</option>
                                </select>
                                <has-error :form="form" field="form.patient_id"></has-error>
                            </div>

                            <div class="form-group">
                                <label>Patient</label>
                                <select class="form-control" v-model="form.patient_id" :disabled="isDisabled">
                                    <option value="0">Select Patient</option>
                                    <option v-for="patient in patients" :value="patient.id">{{ patient.first_name + ' ' + patient.middle_name + ' ' + patient.last_name}}</option>
                                </select>
                                <has-error :form="form" field="form.patient_id"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Requested Test </label>
                                <b-form-select v-model="form.requested_test" text-field="requested_test" class="mb-3" :disabled="isDisabled">
                                <b-form-select-option :value="null">Select Purpose</b-form-select-option>
                                <b-form-select-option value="Traveller">Traveller</b-form-select-option>
                                <b-form-select-option value="Recovery">Recovery</b-form-select-option>
                                <b-form-select-option value="Routine">Routine</b-form-select-option>
                                </b-form-select>
                                <!-- <div class="mt-2">Selected: <strong>{{ selected }}</strong></div> -->
                                <has-error :form="form" field="requested_test"></has-error>
                            </div>

                            <div class="form-group">
                                <label>Test Type</label>
                                <select class="form-control" v-model="form.price_id" :disabled="isDisabled">
                                    <option value="0">Select Type</option>
                                    <option v-for="price in prices" :value="price.id">{{ price.test }}</option>
                                </select>
                                <has-error :form="form" field="form.price_id"></has-error>
                            </div>




                            <div class="form-group">
                                <label> Referred By</label>
                                <input v-model="form.doctors_ref" type="text" name="doctors_ref" placeholder=""
                                    class="form-control" :class="{'is-invaild': form.errors.has('doctor_ref')}" :disabled="isDisabled">
                                <has-error :form="form" field="doctors_ref"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Attach File </label>
                                <input type="file" placeholder=""
                                    class="form-control" :class="{'is-invaild': form.errors.has('attachment')}" v-on:change="onFileChange" name="file">
                                <has-error :form="form" field="attachment"></has-error>
                            </div>

                            <div v-if="file" class="mt-3">
                                <img :src="preview" class="figure-img img-fluid rounded"  style="max-height:100px;">
                            </div>

                            <div class="form-group">
                                <label> Remarks </label>
                                <input v-model="form.comments" type="text" name="comments" placeholder=""
                                    class="form-control" :class="{'is-invaild': form.errors.has('doctor_ref')}">
                                <has-error :form="form" field="comments"></has-error>
                            </div>


                        </div>

                        <div class="modal-footer justify-content-between">
                            <button type="button"  class="btn btn-lg btn-danger" data-dismiss="modal">Close</button>
                            <b-button variant="primary" v-if="!load" class="btn-lg" disabled>
                                <b-spinner small type="grow"></b-spinner>
                                {{  action }}
                            </b-button>
                            <button type="submit" v-if="load" v-show="!editMode" class="btn btn-lg btn-primary">Submit Payment</button>
                            <button type="submit" v-if="load" v-show="editMode" class="btn btn-lg btn-success">Submit Control</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    data() {
        return {

            per_page:1,
            pagination:{
                page:1,
                next:null,
                prev:null,
                perPage:3,

            },
            action: '',
            searchWord: '',
            loading: false,
            editMode: false,
            load: true,
            patients: [],
            prices: [],
            tests: [],
            newtests: [],
            isDisabled: true,
            test: null,
            attachment: null,
            file: '',
            preview: '',
            searchActive: false,
            isSearch: false,
            selected_id: null,
            selected: {},
            laravelData: [],
            form: new Form({
                'id' : '',
                'patient_id': '',
                'price_id': '',
                'payment_mode': '',
                'doctors_ref': '',
                'attachment': '',
                'files': '',
                'sample_status': '',
                'requested_test': '',
                'comments': '',
            })
        }
    },

    methods:{

        resetTable(){
            this.getControls();
        },

        gotoPage(page){
             this.isSearch = false;
            axios.get(this.tests.path+'?page='+page).then((response) => {
                this.tests = response.data.tests;
            })
        },

        gotoURL(url){
             this.isSearch = false;
             axios.get(url).then((response) => {
                this.tests = response.data.tests;
                this.per_page = this.tests.current_page;
            })
        },

        search(){
            this.page=this.pagination.page;
            this.pagination.page=1;
            // this.getControls();
            this.loading = true;
            axios.get(`/getAllControls?s=${this.searchWord}`).then((response) =>{
                this.loading = false;
                this.isSearch = true;
                this.searchActive = true;
                this.tests = response.data.tests;
            }).catch(() =>{
                this.loading = false;
                toast.fire({
                    icon: 'error',
                    title: "search failed"
                })
            })
        },

            createPayment(){

            },

            onFileChange(e){
                // console.log(e);
                // console.log(e.target.files[0]);
                this.file = e.target.files[0];
                let reader = new FileReader();//checking on reader if file is available
                reader.readAsDataURL(this.file);
                reader.onload = e => {
                this.preview = e.target.result;
                // 
                }
            },

            formSubmit() {
                const config = {
                    headers: { 'content-type': 'multipart/form-data' }
                }
                let formData = new FormData();
                formData.append('file', this.file);
                formData.append('id', this.form.id);
                formData.append('comments', this.form.comments);
                formData.append("_method", "put");
                console.log(formData);
                axios.post("/control/test/update/"+ this.selected_id, formData)
                .then(function (response) {
                    $("#createTest").modal("hide");
                    Fire.$emit("loadTest");
                    this.getControls();
                })
                .catch(() => {
                    this.load = true;
                });
            },

            profileUpload(){
                let data = new FormData;
                data.append('image', this.image);
                axios.post('../userprofile', data)
                .then(()=>{
                    window.location = '../profile';
                }).catch(()=>{
                })
            },

        editTest(test){
            this.editMode = true;
            this.form.reset();
            this.selected_id = test.id;
            this.form.fill(test);
            $('#createTest').modal('show');
        },

        createMode(){
            this.editMode = false;
            $('#createTest').modal('show');
        },

        getPrices(){
            this.loading = true;
            axios.get('/getAllPrices').then((response) =>{
                this.loading = false;
                this.prices = response.data.prices
            }).catch(()=>{
                this.loading = false;
                this.$toastr.e("Cannot load Prices", "Error");
            })
        },

        reloadList(){
            this.pagination.page=1;
            this.pagination.perPage=this.per_page;
            this.getControls()
        },

        getControls(){
            this.page=this.pagination.page;
            this.loading = true;
             this.isSearch = false;
             this.pagination.searchWord=this.searchWord
            axios.get('/getAllControls',this.pagination).then((response) =>{
                this.loading = false;
                this.tests = response.data.tests;
                let d= response.data.tests;
                this.pagination.next=d.next_page_url;
                this.pagination.prev=d.prev_page_url;
                this.pagination.perPage=d.per_page;
            }).catch(()=>{
                this.loading = false;
                this.$toastr.e("Cannot load Mixes", "Error");
            })
        },

        nextPage(){
            this.pagination.page++;
            this.getControls();
        },

        prevPage(){
            this.pagination.page--;
            this.getControls();
        },

        getPatients(){
            axios.get('/getAllPatients').then((response) =>{
                this.patients = response.data.patients
            })
        },

        viewTest(test){
            console.log(test.patient.first_name)
            $('#viewTest').modal('show');
            // this.img = 'http://localhost:8000/img/'+user.photo;
            this.test = test;
        },



    },

    mounted() {
       this.getControls();
    },

    created(){
        this.getPrices();
        this.getPatients();
        this.getControls();
        Fire.$on('loadTest', () => {
            this.getPatients();
            this.getPrices();
            this.getControls();
        });
    },

    computed: {
        pages(){
            let list = [];
                if(this.tests){
                    for(let i=1; i<=this.tests.last_page; i++){
                    list.push({value: i, text: 'page ' + i})
                }
            }
                return list;
        }
    }

}
</script>
