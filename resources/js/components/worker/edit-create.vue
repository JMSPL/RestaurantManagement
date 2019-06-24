<template>
    <div class="row">
        <div class="col l12 m12 s12">
            <h3 class="header">{{ operation }} Worker</h3>
            <div class="card horizontal">
                <div class="card-stacked">
                    <div class="card-content">
                        <div class="row">
                            <div class="input-field col s6">
                                <input id="name" name="name" type="text" v-model="worker.name" active>
                                <label for="name">Name</label>
                            </div>
                            <div class="input-field col s6" v-if="worker.username  === this.$store.state.user.username || operation === 'Create'">
                                <input id="username" name="username" type="text" v-model="worker.username">
                                <label for="username">Username</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s6"  v-if="worker.email !== this.$store.state.user.email">
                                <input id="email" name="email" type="email" v-model="worker.email">
                                <label for="email">Email</label>
                            </div>
                            <div class="input-field col s6">
                                <select name="type" id="select" v-model="worker.type" v-if="this.$store.state.user.type === 'manager'">
                                    <option value="" disabled selected>Choose worker type</option>
                                    <option value="manager" :class="{ selected: worker.type === 'manager' }">Manager</option>
                                    <option value="waiter" :class="{ selected: worker.type === 'waiter' }">Waiter</option>
                                    <option value="cook" :class="{ selected: worker.type === 'cook' }">Cook</option>
                                    <option value="cashier" :class="{ selected: worker.type === 'cashier' }">Cashier</option>
                                </select>
                                <label>Worker Type</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s6">
                                <select name="intern" id="select" v-model="worker.intern" v-if="this.$store.state.user.type === 'manager'">
                                    <option value="" disabled selected>Choose if the user is an intern or not</option>
                                    <option value="1" :class="{ selected: worker.intern === 1}">Yes</option>
                                    <option value="0" :class="{ selected: worker.intern === 0}">No</option>
                                </select>
                                <label>Intern</label>
                            </div>
                        <!--</div>
                        <div class="row">-->
                            <div class="file-field input-field col s6">
                                <div class="btn">
                                    <span><i class="material-icons">add_a_photo</i></span>
                                    <input type="file" name="photo" ref="file" @change="handleFileUpload">
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text" placeholder="Upload a photo">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-action right-align">
                        <button class="btn waves-effect waves-light grey" @click="cancel">Cancel</button>
                        <button class="btn waves-effect waves-light" @click="save">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const myScripts = require('../../../../public/js/scripts')
    export default {
        props:['worker','operation'],
        data(){
          return {
              file: '',
              errors: [],
          }
        },
        mounted: function () {
            myScripts.startInstances();
            M.updateTextFields();
        },
        methods:{
            save(){
                if(this.operation === 'Edit'){
                    axios.put('/api/workers/'+this.worker.id, this.worker)
                        .then(res => {
                            if(this.file !== ""){
                                let formData = new FormData();
                                formData.append('file', this.file);

                                axios.post('/api/workers/'+this.worker.id+'/photo', formData,{
                                    headers: {
                                        'Content-type': 'multipart/form-data'
                                    },
                                })
                                    .then(res=>{
                                        M.toast({html: '\"'+this.worker.name+'\" updated!'})
                                        this.$emit('save', res.data);
                                    })
                                    .catch(res => {
                                        this.errors = res.errors;
                                    });
                            }else{
                                this.$emit('save', res.data);
                            }
                        })
                        .catch(res => {
                            this.errors = res.errors;
                        });
                }else if(this.operation === 'Create'){
                    let formData = new FormData();
                    formData.append('file', this.file);

                    axios.post('/api/workers/photo', formData, {
                        headers: {
                            'Content-type': 'multipart/form-data'
                        },
                    })
                        .then(res => {
                            this.worker.photo_url = res.data;
                            axios.post('/api/workers', this.worker)
                                .then(res=>{
                                    M.toast({html: '\"'+this.worker.name+'\" created!'});
                                    this.$emit('save');
                                })
                                .catch(res => {
                                    this.errors = res.errors;
                                });
                        })
                        .catch(res => {
                            this.errors = res.errors;
                        });
                }
            },
            cancel(){
                let request = axios.get('/api/workers/'+this.worker.id);
                request.then(res => this.$emit('cancel', res.data.data));
            },
            handleFileUpload: function(){
                this.file = this.$refs.file.files[0];
            }
        },
    }
</script>
