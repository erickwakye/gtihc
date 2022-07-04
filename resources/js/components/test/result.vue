<template>
    <div class="p-0">
        <div class="card">
            <div class="card-header ui-sortable-handle" style="cursor: move;">
                <h3 class="card-title">
                    <i class="fa fa-hospital-symbol"></i>
                    Results
                </h3>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item mr-1">
                            <!-- <button class="btn btn-sm btn-primary" @click="createMode"><i class="fas fa-plus-circle"></i> Make New Payment</button> -->
                        </li>
                        <li class="nav-item">
                            <div class="input-group mt-0 input-group-sm" style="width: 350px;">
                                <input type="text" name="table_search" v-model="searchWord" class="form-control float-right" placeholder="Search by test">

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
                        <th>Patient</th>
                        <th>TestRequested</th>
                        <th>TestType</th>
                        <th>Batch</th>
                        <th>Remarks</th>
                        <th>Attachment</th>
                        <th>Action</th>
                        <th>Date Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="test in tests.data" :key="test.id">
                            <td>{{ test.id }}</td>
                            <td>{{ test.patient.first_name + ' ' + test.patient.customerId}}</td>
                            <td> {{ test.requested_test }}</td>
                            <td> {{ test.price.test }}</td>
                            <td> {{ test.batch }}</td>
                            <td> {{ test.comments }}</td>
                            <td><img v-bind:src="test.attachment" alt="" width="50" height="50"></td>
                            <td>
                                <button class="btn btn-sm btn-info" @click="viewPrint(test)"> <i class="fa fa-eye"></i>Attachment</button>
                                <button class="btn btn-sm btn-warning" @click="editTest(test)" > <i class="fa fa-edit"></i>Results</button>
                                <button class="btn btn-sm btn-primary mt-1" @click="viewTest(test)"> <i class="fa fa-print"></i>Report</button>
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
            </div>

            <div class="row mt-4">
                <!-- <div class="col-sm6 offset-5" v-if="test">
                    <pagination :data="test" @pagination-change-page="getResults" />
                </div> -->
            </div>
            <loading :loading="loading"></loading>
        </div>
<!-- View Modal -->
        <div class="modal fade" id="viewTest" tabindex="-1" role="dialog" aria-labelledby="viewTestModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                       <!-- Row starts here!! -->

                        <div class="row">
                            <div class="invoice p-3 mb-3">
                                <div class="row" v-if="test">
                                    <div class="col-12">
                                        <h4>
                                            <img v-bind:src="imageLogo" class="brand-image img-circle elevation-3" width="50px" height="70" alt="">
                                            GTI Health Care.
                                            <!-- <qrcode value="https://www.codecheef.org/" :colors="{ colors: { dark: '#0275d8' } }"></qrcode> -->
                                            <!-- <vue-qr v-bind:src='imageLogo' v-bind:logoSrc="imageLogo" text="" :size="200"></vue-qr> -->
                                            <vue-qr v-bind:text="uri" v-bind:callback="testMe" qid="testid" class="float-right" :size="100"></vue-qr>
                                            <!-- <small class="float-right">{{ this.sysDate }}</small> -->
                                        </h4>
                                    </div>
                                </div>

                                <div class="row invoice-info">
                                    <div class="col-sm-4 invoice-col">
                                        <address>
                                            <strong>GTI HC Lab,</strong><br />
                                            HP Nyametei Street F809/1<br />
                                            OSU, ACCRA - GHANA<br />
                                            GPS: GA-032-1300<br />
                                            Toll Free: 0800790555<br />
                                            Email: info@gtihc.com
                                        </address>
                                    </div>

                                    <div class="col-sm-4 invoice-col" v-if="test">
                                        <address>
                                            <strong><b>Client Name:</b></strong>{{ test.patient.first_name + ' ' + test.patient.middle_name + ' ' + test.patient.last_name}}<br />
                                            <strong><b>Client Code:</b></strong>{{ test.patient.customerId }}<br />
                                            <b>Number:</b>{{ test.patient.mobile }}<br />
                                            <b>Address:</b>{{ test.patient.address }}<br />
                                            <b>Gender:</b>{{ test.patient.gender = 'M' ? 'Male': 'Female'}}<br />
                                            <b>Email:</b>{{ test.patient.email }}<br />
                                        </address>
                                    </div>

                                    <div class="col-sm-4 invoice-col" v-if="test">
                                            <b></b><br />
                                            <br />
                                            <b>Batch:</b>{{ test.batch }}<br />
                                            <b>Print Date:</b> {{ this.sysDate }}<br />
                                    </div>

                                </div>

                                <div class="row" v-if="test">
                                    <div class="col-12 table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Test</th>
                                                    <th>Comments</th>
                                                    <th>Results</th>
                                                    <th>Authorised By</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>{{ test.price.test }}</td>
                                                    <td>{{ test.comments }}</td>
                                                    <td>{{ test.results }}</td>
                                                    <td>{{ test.controlled_by }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-muted well well-sm shadow-none" style="margin-top: 10px">
                                        Methods used are included in the MOH, Hefra, FDA, AHPC schedule of Accreditation for this Laboratory.
                                        This document shall not be reproduced except in full, without the written approval from GTI Healthcare Laboratory Management
                                        </p>
                                    </div>

                                <div class="col-6">
                                    <div class="table-responsive">
                                        <table class="table" v-if="test">
                                            <tbody>
                                                <tr>
                                                    <th style="width: 50%">Date Collected:</th>
                                                    <td>{{ test.created_at | date}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Date Received:</th>
                                                    <td>{{ test.created_at | date}}</td>
                                                <tr>
                                                    <th>Date Verified:</th>
                                                    <td>{{ test.updated_at | date}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Date Published:</th>
                                                    <td>{{ test.updated_at | date}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                </div>

                                <div class="row no-print">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-success float-right" @click="sendMail(test)">
                                            <i class="far fa-credit-card"></i> Mail to Client
                                            </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-primary float-right"
                                                    style="margin-right: 5px"
                                                    @click="doPrintVue(test)"
                                                >
                                            <i class="fas fa-download"></i> Generate PDF
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                       <!-- Row ends here!! -->

                    </div>
                </div>
            </div>
        </div>

<!-- Attachment Area -->
<div class="fade modal" id="viewPrint" tabindex="-1" role="dialog" aria-labelledby="viewTestModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                        <div class="modal-body">
                        <div class="row" >
                            <div class="col-md-6" v-if="test">
                                <p><b>Patient:</b> {{ test.patient.first_name }}</p>
                                <p><b>Test:</b> {{ test.price.test }}</p>
                                <p><b>Test Requested:</b> {{ test.requested_test }}</p>
                                <p><b>Sample:</b> {{ test.sample.code }}</p>
                                <p><b>Controlled By:</b> {{ test.controlled_by }}</p>
                                <p><b>Attachment:</b></p>
                                <img v-bind:src="test.attachment" alt="" width="400" height="600">
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
                        <h5 class="modal-title" id="createTestModalLabel" v-show="!editMode">Edit Results </h5>
                        <h5 class="modal-title" id="createTestModalLabel" v-show="editMode">Edit Results</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="!editMode ? createPayment() : updateResult()">
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
                                <select class="form-control" v-model="form.sample_id" :disabled="isDisabled">
                                    <option value="0">Select Sample</option>
                                    <option v-for="sample in samples" :value="sample.id">{{ sample.code }}</option>
                                </select>
                                <has-error :form="form" field="form.price_id"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Results </label>
                                <input v-model="form.results" type="text" name="results" placeholder=""
                                    class="form-control" :class="{'is-invaild': form.errors.has('results')}" >
                                <has-error :form="form" field="results"></has-error>
                            </div>


                        </div>

                        <div class="modal-footer justify-content-between">
                            <button type="button"  class="btn btn-lg btn-danger" data-dismiss="modal">Close</button>
                            <b-button variant="primary" v-if="!load" class="btn-lg" disabled>
                                <b-spinner small type="grow"></b-spinner>
                                {{  action }}
                            </b-button>
                            <button type="submit" v-if="load" v-show="!editMode" class="btn btn-lg btn-primary">Submit Payment</button>
                            <button type="submit" v-if="load" v-show="editMode" class="btn btn-lg btn-success">Submit Results</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import VueQr from 'vue-qr'

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
            colors: '',
            searchWord: '',
            loading: false,
            editMode: false,
            load: true,
            patients: [],
            prices: [],
            tests: [],
            samples: [],
            isDisabled: true,
            searchActive: false,
            isSearch: false,
            selected_id: null,
            id: '',
            test2: '',
            uri:'www.gtihc.com',
            base_url: '',
            imageLogo: 'img/logomain.png',
            isPrint: true,
            sysDate: null,
            batch: null,
            test: null,
            selected: {},
            totalPriceAmount: null,
            laravelData: [],
            form: new Form({
                'id' : '',
                'patient_id': '',
                'price_id': [],
                'prices': [],
                'payment_mode': '',
                'doctor_ref': '',
                'requested_test': '',
                'sample_id': [],
                'results': '',
            })
        }
    },

    methods:{
        resetTable(){
            this.getResults();
        },

        testMe(dataUrl,id){
            dataUrl = 'https:www.gtihc.com';
            id = this.test.patient.customerId;
            console.log(dataUrl, id);
        },

        getTestPage(){
             axios.get('/create-test-mix').then((response) =>{
                this.patients = response.data.patients
            })
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
            this.getResults();
            this.loading = true;
            axios.get(`/getAllPayments?s=${this.searchWord}`).then((response) =>{
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
        createMode(){
            this.editMode = false;
            $('#createTest').modal('show');
        },

        deletePrice(price){
            swal.fire({
                title: 'Are you sure?',
                text: price.test + " will be deleted permanently!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.value) {
                    axios.delete('/delete/price/'+price.id).then(() =>{
                        toast.fire({
                            icon: 'success',
                            title: price.test +" has been deleted sucessfully"
                        })
                        Fire.$emit("loadPrice");
                    }).catch(() =>{
                        toast.fire({
                            icon: 'error',
                            title: price.test +" was unable to be remove, try again later"
                        })
                    })
                }
            })
        },

        editPrice(price){
            this.editMode = true;
            this.form.reset();
            this.form.fill(price);
            $('#createPrice').modal('show');
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

        getPatients(){
            axios.get('/getAllPatients').then((response) =>{
                this.patients = response.data.patients
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

        viewTest(test){
            $('#viewTest').modal('show');
            // this.img = 'http://localhost:8000/img/'+user.photo;
            this.test = test;
            this.base_url = window.location.origin;
            this.uri = this.base_url + '/' + 'get-test-res/' + test.batch;
        },

        viewPrint(test){
             $('#viewPrint').modal('show');
            // this.img = 'http://localhost:8000/img/'+user.photo;
            this.test = test;
        },

        editTest(test){
            this.editMode = true;
            this.form.reset();
            this.form.fill(test);
            $('#createTest').modal('show');
        },

        createPayment(){
            this.action = 'Creating Payment ...'
            this.load = false;
            this.form.post("/payment/create").then((response) => {
                this.load = true;
                this.$toastr.s("Payment made successfully", "Created");
                Fire.$emit("loadPay");
                $("#createTest").modal("hide");
                this.form.reset();
            }).catch(() => {
                this.load = true;
                this.$toastr.e("Cannot make Payment, try again", "Error");
            });
        },

         updateResult(){
            this.action = 'Update Result  ...'
            this.load = false;
            this.form.put("/results/test/update/" +this.form.id).then((response) => {
                this.load = true;
                this.$toastr.s("Result Updated successfully", "Created");
                Fire.$emit("loadTest");
                $("#createTest").modal("hide");
                this.form.reset();
            }).catch(() => {
                this.load = true;
                this.$toastr.e("Cannot Update Result, try again", "Error");
            });
        },

        updatePrice(){
            this.action = 'Update Price ...'
            this.load = false;
            this.form.put("/price/update/" +this.form.id).then((response) => {
                this.load = true;
                this.$toastr.s("Price information updated successfully", "Created");
                Fire.$emit("loadPrice");
                $("#createPrice").modal("hide");
                this.form.reset();
            }).catch(() => {
                this.load = true;
                this.$toastr.e("Cannot update Price information, try again", "Error");
            });
        },

        printReceipt(){
            $('#viewTest').modal('hide');
            this.print = true;
            window.print();
        },

        doPrintVue () {
            var newstr = document.getElementsByClassName('invoice')[0].innerHTML
            document.body.innerHTML = newstr
            window.print()
            // Reload the page to refresh the data
            window.location.reload()
        },

        sendMail(){
            $('#viewTest').modal('hide');
            this.action = 'Update Result  ...'
            this.load = false;
            this.batch = this.test.batch;
            axios.put("/mail/test/update/" +this.batch).then((response) => {
                this.load = true;
                this.$toastr.s("Mail sent successfully!!!", "Created");
                Fire.$emit("loadTest");
                $("#createTest").modal("hide");
                this.form.reset();
                this.getResults();
            }).catch(() => {
                this.load = true;
                this.$toastr.e("Cannot Update Result, try again", "Error");
            });
        },

         nextPage(){
            this.pagination.page++;
            this.getResults();
        },

        prevPage(){
            this.pagination.page--;
            this.getResults();
        },

        callFunction: function () {
            var currentDate = new Date();
            var currentDateWithFormat = new Date().toJSON().slice(0,10).replace(/-/g,'/');
            this.sysDate = currentDateWithFormat;
        },


    },

    mounted() {
        this.callFunction();
        this.getResults();
    },

    created(){
        this.getPrices();
        this.getPatients();
        this.getResults();
        this.getMainSample();
        Fire.$on('loadPay', () => {
            this.getPatients();
            this.getPrices();
            this.getResults();
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
