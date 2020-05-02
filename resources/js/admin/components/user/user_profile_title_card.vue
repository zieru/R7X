<template>
   <div>
   	<span v-if="userdata != null">
   		<router-link tag="a" :to="{name:'users.edit',params:{id: userdata.id}}">{{ userdata.name }}</router-link>
   	</span>
    <span v-else>User Deleted</span>
   </div>
</template>

<script>
    export default {
    	props: ['userdata'],
        name: "UserProfileTitleCard",
    }
</script>
