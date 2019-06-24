<template>
    <div class="row">
        <div class="row">
            <edit-create :worker="user" :operation="'Edit'" @cancel="cancel" @save="save" v-if="editing"></edit-create>
            <password @cancel="cancel" @save="save" :editing="changePassword" v-if="changePassword"></password>
        </div>
        <div class="row">
            <div class="card horizontal small">
                <div class="card-image">
                    <img :src="'/storage/profiles/'+user.photo_url" alt="">
                </div>
                <div class="card-stacked">
                    <div class="card-content">
                        <span class="card-title">Profile</span>
                        <div class="col s12 m6 l6">
                            <p>Name: {{ user.name }}</p>
                            <p>Username: {{ user.username}}</p>
                            <p>Email: {{ user.email}}</p>
                            <p>Type: {{ user.type}}</p>
                        </div>
                        <div class="col s12 m6 l6">
                            <button class="btn waves-effect" @click="edit">Edit</button>
                            <button class="btn waves-effect" @click="password">Change Password</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import EditCreate from './edit-create';
    import Password from './password';
    const myScripts = require('../../../../public/js/scripts');
    export default {
        name: "profile",
        components:{EditCreate,Password},
        data(){
            return {
                editing: false,
                changePassword: false,
                user: Object.assign({}, this.$store.state.user)
            }
        },
        methods:{
            cancel(user){
                this.user = user;
                this.editing = false;
                this.changePassword = false;
            },
            edit(){
                this.changePassword = false;
                this.editing = true;
            },
            password(){
                this.changePassword = true;
                this.editing = false;
            },
            save(user){
                this.$store.dispatch('updateUser', user);
                this.cancel(user)
            }
        },
        mounted(){
            myScripts.startInstances();
        }
    }
</script>

<style scoped>

</style>