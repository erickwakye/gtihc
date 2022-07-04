<template>
    <div class="p-0">
        <div class="card">

            <div class="card-header ui-sortable-handle" style="cursor: move;">
                <h3 class="card-title">
                    <i class="fa fa-file-invoice-dollar mr-1"></i>
                    Make Payment
                </h3>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item mr-1">
                            <button class="btn btn-sm btn-primary" @click="createMode"><i class="fas fa-plus-circle"></i> Make New Payment</button>
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
                        <th>Price</th>
                        <th>TestRequested</th>
                        <th>PaymentMode</th>
                        <th>TestType</th>
                        <th>Batch</th>
                        <th>Action</th>
                        <th>Date Created</th>
                        </tr>

                    </thead>
                    <tbody>
                        <tr v-for="test in tests.data" :key="test.id">
                            <td>{{ test.id }}</td>
                            <td>{{ test.patient.first_name + ' ' + test.patient.customerId}}</td>
                            <td> {{ test.price.amount }}</td>
                            <td> {{ test.requested_test }}</td>
                            <td> {{ test.payment_mode }}</td>
                            <td> {{ test.price.test }}</td>
                            <td> {{ test.batch }}</td>
                            <td>
                                <button class="btn btn-sm btn-info" @click="viewTest(test)"> <i class="fa fa-eye"></i> View</button>
                                <!-- <button class="btn btn-sm btn-warning" @click="editTest(test)" > <i class="fa fa-edit"></i> Edit</button> -->
                                <!-- <button class="btn btn-sm btn-danger" @click="deleteTest(test)"> <i class="fa fa-trash"></i> Delete </button> -->
                                <button class="btn btn-sm btn-info" @click="viewPrint(test)"> <i class="fa fa-print"></i> GenerateReciept </button>
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
                    <pagination :data="test" @pagination-change-page="getPayments" />
                </div> -->
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

<!-- Receipts Template -->
        <div class="fade modal" id="viewPrint" tabindex="-1" role="dialog" aria-labelledby="viewPrintModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="">
                            <div class="" >
                            <div class="ticket" >
                            <img class="imgnew" src="img/logomain.png" alt="Logo">
                            <p class="">+233(0)55 333 9969</p>
                            <div v-if="datas">
                            <p class=""  >{{ datas.patient.first_name + ' ' + datas.patient.middle_name + ' ' + datas.patient.last_name}}</p>
                            </div>
                            <table class="tablenew" >
                                <thead>
                                    <tr>
                                        <th class="quantity">Test.</th>
                                        <th class="">Cost</th>
                                        <!-- <th class="price">GHS</th> -->
                                    </tr>
                                </thead>
                                <tbody >
                                        <tr v-for="test in tests.data" >
                                            <td class="">{{ test.price.test }}</td>
                                            <td class="">{{ test.price.amount }}</td>
                                        </tr>



                                    <tr v-if="costs">
                                        <td class="">TOTAL</td>
                                        <td class="">{{this.costs[0].total}}</td>
                                    </tr>


                                    <tr>
                                        <td>
                                            <p class="centerednew">Thanks for your purchase!
                                            <br>https:www.gtihc.com</p>

                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <button class="btn btn-sm btn-info hidden-print" @click="doPrintVue()"> <i class="fa fa-print"></i> Print Receipt </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>

                                <!-- <button id="btnPrint" class="hidden-print">Print</button> -->
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
                        <h5 class="modal-title" id="createTestModalLabel" v-show="!editMode">Make Payment</h5>
                        <h5 class="modal-title" id="createTestModalLabel" v-show="editMode">Edit Payment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="!editMode ? createPayment() : updatePayment()">
                        <div class="modal-body">

                            <div class="form-group">
                                <label>Patient</label>
                                <select class="form-control" v-model="form.patient_id">
                                    <option value="0">Select Patient</option>
                                    <option v-for="patient in patients" :value="patient.id">{{ patient.first_name }}</option>
                                </select>
                                <has-error :form="form" field="form.patient_id"></has-error>
                            </div>

                            <!-- <div class="form-group">
                                <label>Test</label>
                                <select class="form-control" v-model="form.price_id">
                                    <option value="null">Select Test</option>
                                    <option v-for="price in prices" :value="price.id">{{ price.test + ' - GHS' + price.amount}}</option>
                                </select>
                                <has-error :form="form" field="form.test"></has-error>
                            </div> -->

                            <b-form-group label="Select Test">
                                <b-form-checkbox
                                    v-for="option in prices"
                                    v-model="form.price_id"
                                    :key="option.id"
                                    :value="option.id"
                                    name="flavour-3a"
                                    :value-field="option.amount"
                                >
                                    {{ option.test }}
                                </b-form-checkbox>
                            </b-form-group>

                            <!-- <span>
                                Checked Tests: {{ form.prices}}
                            </span> -->


                           <div class="form-group">
                                <label> Payment Mode </label>
                                <b-form-select v-model="form.payment_mode" text-field="payment_mode" class="mb-3">
                                <b-form-select-option :value="null">Select Payment Form</b-form-select-option>
                                <b-form-select-option value="Cash">Cash</b-form-select-option>
                                <b-form-select-option value="Momo">Momo</b-form-select-option>
                                <b-form-select-option value="Card">Card</b-form-select-option>
                                </b-form-select>
                                <!-- <div class="mt-2">Selected: <strong>{{ selected }}</strong></div> -->
                                <has-error :form="form" field="payment_mode"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Requested Test </label>
                                <b-form-select v-model="form.requested_test" text-field="requested_test" class="mb-3">
                                <b-form-select-option :value="null">Select Purpose</b-form-select-option>
                                <b-form-select-option value="Traveller">Traveller</b-form-select-option>
                                <b-form-select-option value="Recovery">Recovery</b-form-select-option>
                                <b-form-select-option value="Routine">Routine</b-form-select-option>
                                </b-form-select>
                                <!-- <div class="mt-2">Selected: <strong>{{ selected }}</strong></div> -->
                                <has-error :form="form" field="requested_test"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Referred By</label>
                                <input v-model="form.doctor_ref" type="text" name="doctor_ref" placeholder="Doctor/Hospital Reference"
                                    class="form-control" :class="{'is-invaild': form.errors.has('doctor_ref')}">
                                <has-error :form="form" field="doctor_ref"></has-error>
                            </div>

                        </div>

                        <div class="modal-footer justify-content-between">
                            <button type="button"  class="btn btn-lg btn-danger" data-dismiss="modal">Close</button>
                            <b-button variant="primary" v-if="!load" class="btn-lg" disabled>
                                <b-spinner small type="grow"></b-spinner>
                                {{  action }}
                            </b-button>
                            <button type="submit" v-if="load" v-show="!editMode" class="btn btn-lg btn-primary">Submit Payment</button>
                            <button type="submit" v-if="load" v-show="editMode" class="btn btn-lg btn-success">Update Payment</button>
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
            datas: {
                patient: {},
            },
            batch: '',
            isDisabled: true,
            searchActive: false,
            isSearch: false,
            selected_id: null,
            id: '',
            test2: '',
            isPrint: true,
            test: null,
            selected: {},
            totalPriceAmount: null,
            costs: [[0]],
            laravelData: [],
            form: new Form({
                'id' : '',
                'patient_id': '',
                'price_id': [],
                'prices': [],
                'payment_mode': '',
                'doctor_ref': '',
                'requested_test': '',
            })
        }
    },

    methods:{
        resetTable(){
            this.getPayments();
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
            this.getPayments();
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

        getPayments(page = 1){
            this.page=this.pagination.page;
            this.loading = true;
            this.isSearch = false;
            this.pagination.searchWord=this.searchWord;
            axios.get('/getAllPayments',this.pagination).then((response) =>{
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

        getPaymentsByBatch(id){
            axios.get('/get-pay-res/'+id).then((response) =>{
                this.loading = false;
                // this.tests = response.data.tests;
                this.datas = response.data.datas;
                this.costs = response.data.products;
                console.log(this.datas);
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

        viewTest(test){
            $('#viewTest').modal('show');
            this.test = test;
            this.batch = this.test.batch;
            this.getPaymentsByBatch(this.batch);
        },

        viewPrint(test){
            this.test = test;
            this.batch = this.test.batch;
            this.getPaymentsByBatch(this.batch);
            $('#viewPrint').modal('show');
        },

        getTotalTaxes(id) {
            let datas = id;
            let i = 0;
            let calTaxTotal = 0;
            for(i=0; i<=index.length; i++){
                calTaxTotal = i[index];
            }
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

        PrintReceipt(){
            $('#viewTest').modal('hide');
            this.print = true;
            window.print();
        },

        doPrintVue () {
            var newstr = document.getElementsByClassName('ticket')[0].innerHTML
            document.body.innerHTML = newstr
            window.print()
            // Reload the page to refresh the data
            window.location.reload()
        },

         nextPage(){
            this.pagination.page++;
            this.getPayments();
        },

        prevPage(){
            this.pagination.page--;
            this.getPayments();
        },
    },

    mounted() {
        // Fetch initial results
        this.getPayments();
    },

    created(){
        this.getPrices();
        this.getPatients();
        this.getPayments();
        this.getPaymentsByBatch();
        Fire.$on('loadPay', () => {
            this.getPatients();
            this.getPrices();
            this.getPayments();
        this.getPaymentsByBatch();
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
        },

    }



}
</script>
