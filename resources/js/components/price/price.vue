<template>
    <div class="p-0">
        <div class="card">
            <div class="card-header ui-sortable-handle" style="cursor: move;">
                <h3 class="card-title">
                    <i class="fa fa-cogs mr-1"></i>
                    Prices
                </h3>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item mr-1">
                            <button class="btn btn-sm btn-primary" @click="createMode"><i class="fas fa-plus-circle"></i> Add New</button>
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
            <div class="card-body table-responsive p-0">
                <table class="table">
                    <thead>

                        <tr>
                        <th>#</th>
                        <th>Test</th>
                        <th>Amount</th>
                        <th>Vat</th>
                        <th>Tax</th>
                        <th>Levy</th>
                        <th>Date Created</th>
                        </tr>

                    </thead>
                    <tbody>
                        <tr v-for="price in prices" :key="price.id">
                            <td>{{ price.id }}</td>
                            <td>{{ price.test }}</td>
                            <td> {{ price.amount }}</td>
                            <td> {{ price.vat }}</td>
                            <td> {{ price.tax }}</td>
                            <td> {{ price.levy }}</td>

                            <td>
                                <button class="btn btn-sm btn-info" @click="viewPrice(price)"> <i class="fa fa-eye"></i> View</button>
                                <button class="btn btn-sm btn-warning" @click="editPrice(price)" > <i class="fa fa-edit"></i> Edit</button>
                                <button class="btn btn-sm btn-danger" @click="deletePrice(price)"> <i class="fa fa-trash"></i> Delete </button>
                            </td>
                            <td>
                                {{ price.created_at | date }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <loading :loading="loading"></loading>
        </div>
<!-- View Modal -->
        <div class="modal fade" id="viewPrice" tabindex="-1" role="dialog" aria-labelledby="viewSampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><b>Test:</b> {{ price.test }}</p>
                                <p><b>Amount:</b> {{ price.amount }}</p>
                                <p><b>Vat:</b> {{ price.vat }}</p>
                                <p><b>Tax:</b> {{ price.tax }}</p>
                                <p><b>Levy:</b> {{ price.levy }}</p>
                                <p><b>Last Updated:</b> {{ price.updated_at | date }}</p>
                                <p><b>Date Posted:</b> {{ price.created_at | date }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Create Modal -->
        <div class="modal fade" id="createPrice" tabindex="-1" role="dialog" aria-labelledby="createPriceModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createPriceModalLabel" v-show="!editMode">Create Price</h5>
                        <h5 class="modal-title" id="createPriceModalLabel" v-show="editMode">Edit Price</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="!editMode ? createPrice() : updatePrice()">
                        <div class="modal-body">
                            <div class="form-group">
                                <label> Test </label>
                                <input v-model="form.test" type="text" name="test" placeholder="Test"
                                    class="form-control" :class="{'is-invaild': form.errors.has('test')}">
                                <has-error :form="form" field="test"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Amount </label>
                                <input v-model="form.amount" type="text" name="amount" placeholder="Amount"
                                    class="form-control" :class="{'is-invaild': form.errors.has('amount')}">
                                <has-error :form="form" field="amount"></has-error>
                            </div>

                            <div class="form-group">
                                <label> VAT </label>
                                <input v-model="form.vat" type="text" name="vat" placeholder="VAT"
                                    class="form-control" :class="{'is-invaild': form.errors.has('vat')}">
                                <has-error :form="form" field="vat"></has-error>
                            </div>

                            <div class="form-group">
                                <label> TAX </label>
                                <input v-model="form.tax" type="text" name="tax" placeholder="TAX"
                                    class="form-control" :class="{'is-invaild': form.errors.has('tax')}">
                                <has-error :form="form" field="tax"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Levy </label>
                                <input v-model="form.levy" type="text" name="levy" placeholder="Levy"
                                    class="form-control" :class="{'is-invaild': form.errors.has('levy')}">
                                <has-error :form="form" field="levy"></has-error>
                            </div>

                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button"  class="btn btn-lg btn-danger" data-dismiss="modal">Close</button>
                            <b-button variant="primary" v-if="!load" class="btn-lg" disabled>
                                <b-spinner small type="grow"></b-spinner>
                                {{  action }}
                            </b-button>
                            <button type="submit" v-if="load" v-show="!editMode" class="btn btn-lg btn-primary">Save Price</button>
                            <button type="submit" v-if="load" v-show="editMode" class="btn btn-lg btn-success">Update Price</button>
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
            action: '',
            searchWord: '',
            loading: false,
            editMode: false,
            load: true,
            price: {},
            prices: [],
            form: new Form({
                'id' : '',
                'test': '',
                'amount': '',
                'vat': '',
                'levy': '',
                'tax': '',
            })
        }
    },
    methods:{
        search(){
            this.loading = true;
            axios.get('/search/price?s='+this.searchWord).then((response) =>{
                this.loading = false;
                this.prices = response.data.prices
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
            $('#createPrice').modal('show');
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

        viewPrice(price){
            $('#viewPrice').modal('show');
            // this.img = 'http://localhost:8000/img/'+user.photo;
            this.price = price;
        },

        createPrice(){
            this.action = 'Creating Price ...'
            this.load = false;
            this.form.post("/price/create").then((response) => {
                this.load = true;
                this.$toastr.s("Price created successfully", "Created");
                Fire.$emit("loadPrice");
                $("#createPrice").modal("hide");
                this.form.reset();
            }).catch(() => {
                this.load = true;
                this.$toastr.e("Cannot create Price, try again", "Error");
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


    },

    created(){
        this.getPrices();
        Fire.$on('loadPrice', () => {
            this.getPrices();
        });
    }
}
</script>
