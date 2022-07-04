<template>
    <div class="p-0">
        <div class="card">
            <div class="card-header ui-sortable-handle" style="cursor: move;">
                <h3 class="card-title">
                    <i class="fa fa-vials mr-1"></i>
                    Take Sample
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
                                <button class="btn btn-sm btn-warning" @click="editTest(test)" > <i class="fa fa-edit"></i> Take Sample</button>
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
                        <h5 class="modal-title" id="createTestModalLabel" v-show="!editMode">Take Sample</h5>
                        <h5 class="modal-title" id="createTestModalLabel" v-show="editMode">Take Sample</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="!editMode ? createPayment() : updateSample()">
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
                                <input v-model="form.doctors_ref" type="text" name="doctor_ref" placeholder=""
                                    class="form-control" :class="{'is-invaild': form.errors.has('doctor_ref')}" :disabled="isDisabled">
                                <has-error :form="form" field="doctor_ref"></has-error>
                            </div>

                            <div class="form-group">
                                <label>Sample Used</label>
                                <select class="form-control" v-model="form.sample_id" >
                                    <option value="0">Select Sample</option>
                                    <option v-for="sample in samples" :value="sample.id">{{ sample.code }}</option>
                                </select>
                                <has-error :form="form" field="form.price_id"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Sample Status </label>
                                <b-form-select v-model="form.sample_status" text-field="sample_status" class="mb-3" >
                                <b-form-select-option :value="null">Select Status</b-form-select-option>
                                <b-form-select-option value="1">Taken</b-form-select-option>
                                <b-form-select-option value="0">Not Taken</b-form-select-option>
                                </b-form-select>
                                <!-- <div class="mt-2">Selected: <strong>{{ selected }}</strong></div> -->
                                <has-error :form="form" field="sample_status"></has-error>
                            </div>


                        </div>

                        <div class="modal-footer justify-content-between">
                            <button type="button"  class="btn btn-lg btn-danger" data-dismiss="modal">Close</button>
                            <b-button variant="primary" v-if="!load" class="btn-lg" disabled>
                                <b-spinner small type="grow"></b-spinner>
                                {{  action }}
                            </b-button>
                            <button type="submit" v-if="load" v-show="!editMode" class="btn btn-lg btn-primary">Submit Payment</button>
                            <button type="submit" v-if="load" v-show="editMode" class="btn btn-lg btn-success">Submit Sample</button>
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
            isSearch: false,
            selected_id: null,
            prices: [],
            samples: [],
            tests: [],
            newtests: [],
            isDisabled: true,
            test: null,
            selected: {},
            laravelData: [],
            form: new Form({
                'id' : '',
                'patient_id': '',
                'price_id': '',
                'sample_id': '',
                'payment_mode': '',
                'doctors_ref': '',
                'sample_status': '',
                'requested_test': '',
            })
        }
    },

    methods:{
        resetTable(){
            this.getResults();
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
            this.getSamples();
            this.loading = true;
            axios.get(`/getAllSamples?s=${this.searchWord}`).then((response) =>{
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

        editTest(test){
            this.editMode = true;
            this.form.reset();
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

        getMainSample(){
            this.loading = true;
            axios.get('/getSamples').then((response) =>{
                this.loading = false;
                this.samples = response.data.samples
            }).catch(()=>{
                this.loading = false;
                this.$toastr.e("Cannot load Prices", "Error");
            })
        },

        getSamples(page = 1){
           this.page=this.pagination.page;
            this.loading = true;
            this.isSearch = false;
            this.pagination.searchWord=this.searchWord;
            axios.get('/getAllSamples',this.pagination).then((response) =>{
                this.loading = false;
                this.tests = response.data.tests;
                let d= response.data.tests;
                this.pagination.next=d.next_page_url;
                this.pagination.prev=d.prev_page_url;
                this.pagination.perPage=d.per_page;
            }).catch(()=>{
                this.loading = false;
                this.$toastr.e("Cannot load Payments", "Error");
            })
        },

        getResults(page = 1){
            this.page=this.pagination.page;
            this.loading = true;
            this.isSearch = false;
            this.pagination.searchWord=this.searchWord;
            axios.get('/getAllResults',this.pagination).then((response) =>{
                this.loading = false;
                this.tests = response.data.tests;
                let d= response.data.tests;
                this.pagination.next=d.next_page_url;
                this.pagination.prev=d.prev_page_url;
                this.pagination.perPage=d.per_page;
            }).catch(()=>{
                this.loading = false;
                this.$toastr.e("Cannot load Payments", "Error");
            })
        },

        nextPage(){
            this.pagination.page++;
            this.getSamples();
        },

        prevPage(){
            this.pagination.page--;
            this.getSamples();
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

        updateSample(){
            this.action = 'Update Sample Test ...'
            this.load = false;
            this.form.put("/sample/test/update/" +this.form.id).then((response) => {
                this.load = true;
                this.$toastr.s("Sample Test Taken successfully", "Created");
                Fire.$emit("loadTest");
                $("#createTest").modal("hide");
                this.form.reset();
            }).catch(() => {
                this.load = true;
                this.$toastr.e("Cannot Sample Test Taken, try again", "Error");
            });
        },


    },

    mounted() {
        // Fetch initial results
        console.log('before send resuest');
        this.getSamples();
    },

    created(){
        this.getPrices();
        this.getPatients();
        this.getSamples();
        this.getMainSample();
        Fire.$on('loadTest', () => {
            this.getPatients();
            this.getPrices();
            this.getSamples();
            this.getMainSample();
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
