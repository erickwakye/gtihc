<template>
    <div class="p-0">
        <div class="card">
            <div class="card-header ui-sortable-handle" style="cursor: move;">



            <div class="row">
                            <div class="invoice p-3 mb-3">
                                <div class="row" v-if="tests">
                                    <div class="col-12">
                                        <h4>
                                            <img v-bind:src="imageLogo" class="brand-image img-circle elevation-3" width="50px" height="70" alt="">
                                            GTI Health Care.
                                            <!-- <qrcode value="https://www.codecheef.org/" :colors="{ colors: { dark: '#0275d8' } }"></qrcode> -->
                                            <!-- <vue-qr v-bind:src='imageLogo' v-bind:logoSrc="imageLogo" text="" :size="200"></vue-qr> -->
                                            <vue-qr v-bind:text="uri"  qid="testid" class="float-right" :size="100"></vue-qr>
                                            <!-- <small class="float-right">{{ this.sysDate }}</small> -->
                                        </h4>
                                    </div>
                                 </div>
                                <!--<div>
                                    <tr v-for="test in tests">
                                         <b>Number:</b>{{ test.batch}}<br />
                                    </tr>
                                </div> -->

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

                                    <div class="col-sm-4 invoice-col" v-if="datas">
                                        <address>
                                            <strong><b>Client Name:</b></strong>{{ datas.patient.first_name + ' ' + datas.patient.middle_name + ' ' + datas.patient.last_name}}<br />
                                            <strong><b>Client Code:</b></strong>{{ datas.patient.customerId }}<br />
                                            <b>Number:</b>{{ datas.patient.mobile }}<br />
                                            <b>Address:</b>{{ datas.patient.address }}<br />
                                            <b>Gender:</b>{{ datas.patient.gender = 'M' ? 'Male': 'Female'}}<br />
                                            <b>Email:</b>{{ datas.patient.email }}<br />
                                        </address>
                                    </div>

                                    <div class="col-sm-4 invoice-col" v-if="datas">
                                            <b></b><br />
                                            <br />
                                            <b>Batch:</b>{{ datas.batch }}<br />
                                            <b>Print Date:</b> {{ this.sysDate }}<br />
                                    </div>

                                </div>

                                <div class="row" v-for="test in tests">
                                    <div class="col-12 table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr >
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
                                        <table class="table" v-if="datas">
                                            <tbody>
                                                <tr>
                                                    <th style="width: 50%">Date Collected:</th>
                                                    <td>{{ datas.created_at | date}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Date Received:</th>
                                                    <td>{{ datas.created_at | date}}</td>
                                                <tr>
                                                    <th>Date Verified:</th>
                                                    <td>{{ datas.updated_at | date}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Date Published:</th>
                                                    <td>{{ datas.updated_at | date}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                </div>

                                <div class="row no-print">
                                    <div class="col-12">
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
        </div>



    </div>
    </div>
</template>

<script>
import VueQr from 'vue-qr'

export default {
    props: [
        'tests',
        'datas'
    ],
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
            newdatas: [],
            samples: [],
            isDisabled: true,
            searchActive: false,
            isSearch: false,
            selected_id: null,
            id: '',
            test2: '',
            uri:'www.gtihc.com',
            base_url: '',
            imageLogo: '/img/logomain.png',
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

        // getResults(page = 1){
        //     this.page=this.pagination.page;
        //     this.loading = true;
        //     this.isSearch = false;
        //     this.pagination.searchWord=this.searchWord;
        //     axios.get('/getAllResults',this.pagination).then((response) =>{
        //         this.loading = false;
        //         this.tests = response.data.tests;
        //         let d= response.data.tests;
        //         this.pagination.next=d.next_page_url;
        //         this.pagination.prev=d.prev_page_url;
        //         this.pagination.perPage=d.per_page;
        //     }).catch(()=>{
        //         this.loading = false;
        //         this.$toastr.e("Cannot load Payments", "Error");
        //     })
        // },

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
            console.log(test.patient.first_name)
            $('#viewTest').modal('show');
            // this.img = 'http://localhost:8000/img/'+user.photo;
            this.test = test;
            this.base_url = window.location.origin;
            this.uri = this.base_url + '/' + 'get-test-results/' + test.batch;
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
            this.form.post("/results/test/update/" +this.form.id).then((response) => {
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
            console.log(currentDate);
            var currentDateWithFormat = new Date().toJSON().slice(0,10).replace(/-/g,'/');
            console.log(currentDateWithFormat);
            this.sysDate = currentDateWithFormat;
        },


    },

    mounted() {
        this.callFunction();
        // this.getResults();
        console.log(this.tests);
    },

    created(){
        this.getPrices();
        this.getPatients();
        // this.getResults();
        this.getMainSample();
        Fire.$on('loadPay', () => {
            this.getPatients();
            this.getPrices();
            // this.getResults();
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
