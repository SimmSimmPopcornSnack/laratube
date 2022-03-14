<template>
    <div class="media d-flex my-3 w-full">
        <div>
            <avatar :username="comment.user.name" :size="30" class="mr-3"></avatar>
        </div>
        <div class="media-body w-full" style="margin-left: 10px;">
            <h6 class="mt-0">{{ comment.user.name }}</h6>
            <small>
                {{ comment.body }}
            </small>
            <div class="d-flex">
                <votes :default_votes="comment.votes" :entity_id="comment.id" :entity_owner="comment.user_id"></votes>
                <button class="btn btn-sm btn-default ml-2">Add reply</button>
            </div>
            <replies :comment="comment"></replies>
        </div>
    </div>
</template>

<script>
import Avatar from "vue-avatar";
import Replies from "./replies.vue";
export default {
    components: {
        Avatar,
        Replies,
    },
    props: {
        comment: {
            required: true,
            default: () => ({}),
        }
    }
};
</script>
