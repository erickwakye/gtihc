<template>
    <div class="p-0">
        <div class="card">
            <div class="card-header ui-sortable-handle" style="cursor: move;">
                <h3 class="card-title">
                    <i class="fa fa-cogs mr-1"></i>
                    Samples
                </h3>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item mr-1">
                            <button class="btn btn-sm btn-primary" @click="createMode"><i class="fas fa-plus-circle"></i> Add New</button>
                        </li>
                        <li class="nav-item">
                            <div class="input-group mt-0 input-group-sm" style="width: 350px;">
                                <input type="text" name="table_search" v-model="searchWord" class="form-control float-right" placeholder="Search by code, description">

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
                        <th>Code</th>
                        <th>Description</th>
                        <th>Action</th>
                        <th>Date Created</th>
                        </tr>

                    </thead>
                    <tbody>
                        <tr v-for="sample in samples" :key="sample.id">
                            <td>{{ sample.id }}</td>
                            <td>{{ sample.code }}</td>
                            <td> {{ sample.description }}</td>

                            <td>
                                <button class="btn btn-sm btn-info" @click="viewSample(sample)"> <i class="fa fa-eye"></i> View</button>
                                <button class="btn btn-sm btn-warning" @click="editSample(sample)" > <i class="fa fa-edit"></i> Edit</button>
                                <button class="btn btn-sm btn-danger" @click="deleteSample(sample)"> <i class="fa fa-trash"></i> Delete </button>
                            </td>
                            <td>
                                {{ sample.created_at | date }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <loading :loading="loading"></loading>
        </div>
<!-- View Modal -->
        <div class="modal fade" id="viewSample" tabindex="-1" role="dialog" aria-labelledby="viewSampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><b>Code:</b> {{ sample.code }}</p>
                                <p><b>Description:</b> {{ sample.description }}</p>
                                <p><b>Last Updated:</b> {{ sample.updated_at | date }}</p>
                                <p><b>Date Posted:</b> {{ sample.created_at | date }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Create Modal -->
        <div class="modal fade" id="createSample" tabindex="-1" role="dialog" aria-labelledby="createSampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createSampleModalLabel" v-show="!editMode">Create Sample</h5>
                        <h5 class="modal-title" id="createSampleModalLabel" v-show="editMode">Edit Sample</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="!editMode ? createSample() : updateSample()">
                        <div class="modal-body">
                            <div class="form-group">
                                <label> Code </label>
                                <input v-model="form.code" type="text" name="code" placeholder="Code"
                                    class="form-control" :class="{'is-invaild': form.errors.has('code')}">
                                <has-error :form="form" field="code"></has-error>
                            </div>

                            <div class="form-group">
                                <label> Description </label>
                                <input v-model="form.description" type="text" name="description" placeholder="Description"
                                    class="form-control" :class="{'is-invaild': form.errors.has('description')}">
                                <has-error :form="form" field="description"></has-error>
                            </div>

                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button"  class="btn btn-lg btn-danger" data-dismiss="modal">Close</button>
                            <b-button variant="primary" v-if="!load" class="btn-lg" disabled>
                                <b-spinner small type="grow"></b-spinner>
                                {{  action }}
                            </b-button>
                            <button type="submit" v-if="load" v-show="!editMode" class="btn btn-lg btn-primary">Save Sample</button>
                            <button type="submit" v-if="load" v-show="editMode" class="btn btn-lg btn-success">Update Sample</button>
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
            patient: {},
            patients: [],
            sample: {},
            samples: [],
            form: new Form({
                'id' : '',
                'code': '',
                'description': '',
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
            $('#createSample').modal('show');
        },

        deletePatient(sample){
            swal.fire({
                title: 'Are you sure?',
                text: sample.code + " will be deleted permanently!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.value) {
                    axios.delete('/delete/sample/'+sample.id).then(() =>{
                        toast.fire({
                            icon: 'success',
                            title: sample.code +" has been deleted sucessfully"
                        })
                        Fire.$emit("loadUser");
                    }).catch(() =>{
                        toast.fire({
                            icon: 'error',
                            title: sample.code +" was unable to be remove, tyr again later"
                        })
                    })
                }
            })
        },

        editSample(sample){
            this.editMode = true;
            this.form.reset();
            this.form.fill(sample);
            $('#createSample').modal('show');
        },

        getSamples(){
            this.loading = true;
            axios.get('/getSamples').then((response) =>{
                this.loading = false;
                this.samples = response.data.samples
            }).catch(()=>{
                this.loading = false;
                this.$toastr.e("Cannot load types", "Error");
            })
        },

        viewSample(sample){
            $('#viewSample').modal('show');
            // this.img = 'http://localhost:8000/img/'+user.photo;
            this.sample = sample;
        },

        createSample(){
            this.action = 'Creating Sample ...'
            this.load = false;
            this.form.post("/sample/create").then((response) => {
                this.load = true;
                this.$toastr.s("Sample created successfully", "Created");
                Fire.$emit("loadSample");
                $("#createSample").modal("hide");
                this.form.reset();
            }).catch(() => {
                this.load = true;
                this.$toastr.e("Cannot create Sample, try again", "Error");
            });
        },

        updateSample(){
            this.action = 'Update Type ...'
            this.load = false;
            this.form.put("/sample/update/" +this.form.id).then((response) => {
                this.load = true;
                this.$toastr.s("Sample information updated successfully", "Created");
                Fire.$emit("loadSample");
                $("#createSample").modal("hide");
                this.form.reset();
            }).catch(() => {
                this.load = true;
                this.$toastr.e("Cannot update Sample information, try again", "Error");
            });
        },


    },

    created(){
        this.getSamples();
        Fire.$on('loadSample', () => {
            // this.getUsers();
            this.getSamples();
        });
    }
}
</script>
