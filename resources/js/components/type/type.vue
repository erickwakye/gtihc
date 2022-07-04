<template>
    <div class="p-0">
        <div class="card">
            <div class="card-header ui-sortable-handle" style="cursor: move;">
                <h3 class="card-title">
                    <i class="fa fa-cogs mr-1"></i>
                    Types
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
                        <tr v-for="type in types" :key="type.id">
                            <td>{{ type.id }}</td>
                            <td>{{ type.code }}</td>
                            <td> {{ type.description }}</td>

                            <td>
                                <button class="btn btn-sm btn-info" @click="viewType(type)"> <i class="fa fa-eye"></i> View</button>
                                <button class="btn btn-sm btn-warning" @click="editType(type)" > <i class="fa fa-edit"></i> Edit</button>
                                <button class="btn btn-sm btn-danger" @click="deleteType(type)"> <i class="fa fa-trash"></i> Delete </button>
                            </td>
                            <td>
                                {{ type.created_at | date }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <loading :loading="loading"></loading>
        </div>
<!-- View Modal -->
        <div class="modal fade" id="viewType" tabindex="-1" role="dialog" aria-labelledby="viewTypeModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><b>Code:</b> {{ type.code }}</p>
                                <p><b>Description:</b> {{ type.description }}</p>
                                <p><b>Last Updated:</b> {{ type.updated_at | date }}</p>
                                <p><b>Date Posted:</b> {{ type.created_at | date }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Create Modal -->
        <div class="modal fade" id="createType" tabindex="-1" role="dialog" aria-labelledby="createTypeModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createTypeModalLabel" v-show="!editMode">Create Type</h5>
                        <h5 class="modal-title" id="createTypeModalLabel" v-show="editMode">Edit Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="!editMode ? createType() : updateType()">
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
                            <button type="submit" v-if="load" v-show="!editMode" class="btn btn-lg btn-primary">Save Type</button>
                            <button type="submit" v-if="load" v-show="editMode" class="btn btn-lg btn-success">Update Type</button>
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
            type: {},
            types: [],
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
            axios.get('/search/type?s='+this.searchWord).then((response) =>{
                this.loading = false;
                this.types = response.data.types
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
            $('#createType').modal('show');
        },

        deleteType(type){
            swal.fire({
                title: 'Are you sure?',
                text: patient.code + " will be deleted permanently!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.value) {
                    axios.delete('/delete/type/'+type.id).then(() =>{
                        toast.fire({
                            icon: 'success',
                            title: type.first_name +" has been deleted sucessfully"
                        })
                        Fire.$emit("loadType");
                    }).catch(() =>{
                        toast.fire({
                            icon: 'error',
                            title: type.code +" was unable to be remove, tyr again later"
                        })
                    })
                }
            })
        },

        editType(type){
            this.editMode = true;
            this.form.reset();
            this.form.fill(type);
            $('#createType').modal('show');
        },

        getTypes(){
            this.loading = true;
            axios.get('/getAllTypes').then((response) =>{
                this.loading = false;
                this.types = response.data.types
            }).catch(()=>{
                this.loading = false;
                this.$toastr.e("Cannot load types", "Error");
            })
        },

        viewType(type){
            $('#viewType').modal('show');
            // this.img = 'http://localhost:8000/img/'+user.photo;
            this.type = type;
        },

        createType(){
            this.action = 'Creating Type ...'
            this.load = false;
            this.form.post("/type/create").then((response) => {
                this.load = true;
                this.$toastr.s("Type created successfully", "Created");
                Fire.$emit("loadType");
                $("#createType").modal("hide");
                this.form.reset();
            }).catch(() => {
                this.load = true;
                this.$toastr.e("Cannot create type, try again", "Error");
            });
        },

        updateType(){
            this.action = 'Update Type ...'
            this.load = false;
            this.form.put("/type/update/" +this.form.id).then((response) => {
                this.load = true;
                this.$toastr.s("Type information updated successfully", "Created");
                Fire.$emit("loadType");
                $("#createType").modal("hide");
                this.form.reset();
            }).catch(() => {
                this.load = true;
                this.$toastr.e("Cannot update Type information, try again", "Error");
            });
        },


    },

    created(){
        this.getTypes();
        Fire.$on('loadType', () => {
            // this.getUsers();
            this.getTypes();
        });
    }
}
</script>
