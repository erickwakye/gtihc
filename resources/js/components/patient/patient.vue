<template>
    <div class="p-0">
        <div class="card">
            <div class="card-header ui-sortable-handle" style="cursor: move;">
                <h3 class="card-title">
                    <i class="fas fa-users-medical mr-1"></i><i class="fas fa-hospital-user"></i>
                    Patients
                </h3>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item mr-1">
                            <button class="btn btn-sm btn-primary" @click="createMode"><i class="fas fa-plus-circle"></i> Add New</button>
                        </li>
                        <li class="nav-item">
                            <div class="input-group mt-0 input-group-sm" style="width: 350px;">
                                <input type="text" name="table_search" v-model="searchWord" class="form-control float-right" placeholder="Search by name, email, number">

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
                        <th>Name</th>
                        <th>User Code</th>
                        <th>Gender</th>
                        <!-- <th>Address</th> -->
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>ID Type</th>
                        <th>ID Number</th>
                        <!-- <th>Nationality</th>
                        <th>Digital Address</th> -->
                        <th>Action</th>
                        <th>Date Created</th>
                        </tr>

                    </thead>
                    <tbody>
                        <tr v-for="patient in patients" :key="user.id">
                            <td>{{ patient.id }}</td>
                            <td> {{ patient.first_name + ' ' + patient.middle_name +' ' + patient.last_name }}</td>
                            <td>{{ patient.customerId }}</td>
                            <td>{{ patient.gender }}</td>
                            <!-- <td>{{ patient.address }}</td> -->
                            <td>{{ patient.mobile }}</td>
                            <td>{{ patient.email }}</td>
                            <td>{{ patient.id_type }}</td>
                            <td>{{ patient.id_number }}</td>
                            <!-- <td>{{ patient.alt_mobile }}</td>
                            <td>{{ patient.digital_address }}</td> -->
                            <td>
                                <button class="btn btn-sm btn-info" @click="viewPatient(patient)"> <i class="fa fa-eye"></i> View</button>
                                <button class="btn btn-sm btn-warning" @click="editPatient(patient)" > <i class="fa fa-edit"></i> Edit</button>
                                <button class="btn btn-sm btn-danger" @click="deletePatient(patient)"> <i class="fa fa-trash"></i> Delete </button>
                            </td>
                            <td>
                                {{ patient.created_at | date }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <loading :loading="loading"></loading>
        </div>
<!-- View Modal -->
        <div class="modal fade" id="viewPatient" tabindex="-1" role="dialog" aria-labelledby="viewPatientModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><b>Patient ID:</b> {{ patient.customerId }}</p>
                                <p><b>Name:</b> {{ patient.first_name + ' ' + patient.last_name + ' ' + patient.middle_name }}</p>
                                <p><b>Email:</b> {{ patient.email }}</p>
                                <p><b>Address:</b> {{ patient.address }}</p>
                                <p><b>Gender:</b> {{ patient.gender }}</p>
                                <p><b>Mobile:</b> {{ patient.mobile }}</p>
                                <p><b>ID Type:</b> {{ patient.id_type }}</p>
                                <p><b>ID Number:</b> {{ patient.id_number }}</p>
                                <p><b>Nationality:</b> {{ patient.alt_mobile }}</p>
                                <p><b>Digital Address:</b> {{ patient.digital_address }}</p>
                                <p><b>Last Updated:</b> {{ patient.updated_at | date }}</p>
                                <p><b>Date Posted:</b> {{ patient.created_at | date }}</p>
                            </div>

                            <div class="col-md-6">
                                <img :src="img" class="img-circle">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Create Modal -->
        <div class="modal fade" id="createPatient" tabindex="-1" role="dialog" aria-labelledby="createPatientModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createPatientModalLabel" v-show="!editMode">Create Patient</h5>
                        <h5 class="modal-title" id="createPatientModalLabel" v-show="editMode">Edit Patient</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="!editMode ? createPatient() : updatePatient()">
                        <div class="modal-body">
                            <div class="form-group">
                                <label> First Name </label>
                                <input v-model="form.first_name" type="text" name="first_name" placeholder="First Name"
                                    class="form-control" :class="{'is-invaild': form.errors.has('first_name')}">
                                <has-error :form="form" field="first_name"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Middle Name </label>
                                <input v-model="form.middle_name" type="text" name="middle_name" placeholder="Middle Name"
                                    class="form-control" :class="{'is-invaild': form.errors.has('middle_name')}">
                                <has-error :form="form" field="middle_name"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Last Name </label>
                                <input v-model="form.last_name" type="text" name="last_name" placeholder="Last Name"
                                    class="form-control" :class="{'is-invaild': form.errors.has('last_name')}">
                                <has-error :form="form" field="last_name"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Email </label>
                                <input v-model="form.email" type="text" name="email" placeholder="Email"
                                    class="form-control" :class="{'is-invaild': form.errors.has('email')}">
                                <has-error :form="form" field="email"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Mobile Number </label>
                                <input v-model="form.mobile" type="text" name="mobile" placeholder="Mobile Number"
                                    class="form-control" :class="{'is-invaild': form.errors.has('mobile')}">
                                <has-error :form="form" field="mobile"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Choose ID </label>
                                <b-form-select v-model="form.id_type" text-field="id_type" class="mb-3">
                                <b-form-select-option :value="null">Select the ID Type</b-form-select-option>
                                <b-form-select-option value="GhanaCard">Ghana Card</b-form-select-option>
                                <b-form-select-option value="Passport">Passport</b-form-select-option>
                                <b-form-select-option value="Voters">Voters</b-form-select-option>
                                </b-form-select>
                                <!-- <div class="mt-2">Selected: <strong>{{ selected }}</strong></div> -->
                                <has-error :form="form" field="id_type"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Gender </label>
                                <b-form-select v-model="form.gender" text-field="form.gender" class="mb-3">
                                <b-form-select-option :value="null">Select Gender</b-form-select-option>
                                <b-form-select-option value="M">Male</b-form-select-option>
                                <b-form-select-option value="F">Female</b-form-select-option>
                                </b-form-select>
                                <!-- <div class="mt-2">Selected: <strong>{{ selected }}</strong></div> -->
                                <has-error :form="form" field="form.gender"></has-error>
                            </div>

                            <div class="form-group">
                                <label> ID Number </label>
                                <input v-model="form.id_number" type="text" name="id_number" placeholder="ID Number"
                                    class="form-control" :class="{'is-invaild': form.errors.has('id_number')}">
                                <has-error :form="form" field="id_number"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Nationality </label>
                                <input v-model="form.alt_mobile" type="text" name="alt_mobile" placeholder="Nationality"
                                    class="form-control" :class="{'is-invaild': form.errors.has('alt_mobile')}">
                                <has-error :form="form" field="alt_mobile"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Digital Address </label>
                                <input v-model="form.digital_address" type="text" name="digital_address" placeholder="Digital Address"
                                    class="form-control" :class="{'is-invaild': form.errors.has('digital_address')}">
                                <has-error :form="form" field="digital_address"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Address </label>
                                <input v-model="form.address" type="text" name="address" placeholder="Address"
                                    class="form-control" :class="{'is-invaild': form.errors.has('address')}">
                                <has-error :form="form" field="address"></has-error>
                            </div>



                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button"  class="btn btn-lg btn-danger" data-dismiss="modal">Close</button>
                            <b-button variant="primary" v-if="!load" class="btn-lg" disabled>
                                <b-spinner small type="grow"></b-spinner>
                                {{  action }}
                            </b-button>
                            <button type="submit" v-if="load" v-show="!editMode" class="btn btn-lg btn-primary">Save Patient</button>
                            <button type="submit" v-if="load" v-show="editMode" class="btn btn-lg btn-success">Update Patient</button>
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
            img: 'http://localhost:8000/img/avatar.jpg',
            user: {},
            patient: {},
            users: [],
            patients: [],
            roles: [],
            permissions:[],
            selected: null,
                options: [
                { value: null, text: 'Please select an option' },
                { value: 'ghana_card', text: 'Ghana Card' },
                { value: 'passport', text: 'Passport' },
                { value: 'voters_id', text: 'Voters'}
                ],
            form: new Form({
                'id' : '',
                'first_name': '',
                'middle_name': '',
                'last_name': '',
                'customerId': '',
                'gender': '',
                'email': '',
                'mobile': '',
                'id_type': '',
                'id_number': '',
                'alt_mobile': '',
                'address': '',
                'digital_address': '',
            })
        }
    },
    methods:{
        search(){
            this.loading = true;
            axios.get('/search/patient?s='+this.searchWord).then((response) =>{
                this.loading = false;
                this.patients = response.data.patients
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
            $('#createPatient').modal('show');
        },

        deletePatient(patient){
            swal.fire({
                title: 'Are you sure?',
                text: patient.first_name + " will be deleted permanently!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.value) {
                    axios.delete('/delete/patient/'+patient.id).then(() =>{
                        toast.fire({
                            icon: 'success',
                            title: patient.first_name +" has been deleted sucessfully"
                        })
                        Fire.$emit("loadUser");
                    }).catch(() =>{
                        toast.fire({
                            icon: 'error',
                            title: patient.first_name +" was unable to be remove, tyr again later"
                        })
                    })
                }
            })
        },

        editPatient(patient){
            this.editMode = true;
            this.form.reset();
            this.form.fill(patient);
            $('#createPatient').modal('show');
        },

        getPatients(){
            this.loading = true;
            axios.get('/getAllPatients').then((response) =>{
                this.loading = false;
                this.patients = response.data.patients
            }).catch(()=>{
                this.loading = false;
                this.$toastr.e("Cannot load patients", "Error");
            })
        },

        viewPatient(patient){
            $('#viewPatient').modal('show');
            // this.img = 'http://localhost:8000/img/'+user.photo;
            this.patient = patient;
        },
        getRoles(){
            axios.get('/getAllRoles').then((response) =>{
                this.roles = response.data.roles
            });
        },
        getPermissions(){
            axios.get('/getAllPermissions').then((response) =>{
                this.permissions = response.data.permissions
            });
        },

        createPatient(){
            this.action = 'Creating Patient ...'
            this.load = false;
            this.form.post("/patient/create").then((response) => {
                this.load = true;
                this.$toastr.s("Patient created successfully", "Created");
                Fire.$emit("loadUser");
                $("#createPatient").modal("hide");
                this.form.reset();
            }).catch(() => {
                this.load = true;
                this.$toastr.e("Cannot create user, try again", "Error");
            });
        },

        updatePatient(){
            this.action = 'Update Patient ...'
            this.load = false;
            this.form.put("/patient/update/" +this.form.id).then((response) => {
                this.load = true;
                this.$toastr.s("Patient information updated successfully", "Created");
                Fire.$emit("loadUser");
                $("#createPatient").modal("hide");
                this.form.reset();
            }).catch(() => {
                this.load = true;
                this.$toastr.e("Cannot update Patient information, try again", "Error");
            });
        },

        updateUser(){
            this.action = 'Update user ...'
            this.load = false;
            this.form.put("/account/update/" +this.form.id).then((response) => {
                this.load = true;
                this.$toastr.s("user information updated succefully", "Created");
                Fire.$emit("loadUser");
                $("#createUser").modal("hide");
                this.form.reset();
            }).catch(() => {
                this.load = true;
                this.$toastr.e("Cannot update user information, try again", "Error");
            });
        },


    },

    created(){
        // this.getUsers();
        this.getPatients();
        this.getRoles();
        this.getPermissions();
        Fire.$on('loadUser', () => {
            // this.getUsers();
            this.getPatients();
        });
    }
}
</script>
