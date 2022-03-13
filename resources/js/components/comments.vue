<template>
    <div class="card mt-5 p-5">
        <div v-if="auth" class="from-inline my-4 w-full d-flex">
            <input v-model="newComment" type="text" class="form-control from-control-sm w-80">
            <button @click="addComment" class="btn btn-sm btn-primary">
                <small>Add comment</small>
            </button>
        </div>
        <div class="media d-flex my-3 w-full" v-for="comment in comments.data">
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
        <div class="text-center">
            <button v-if="comments.next_page_url" @click="fetchComments" class="btn btn-success">
                Load More
            </button>
            <span v-else>No more comments to show :)</span>
        </div>
    </div>

</template>

<script>
import Avatar from "vue-avatar";
import Replies from "./replies.vue";
import axios from 'axios';
export default {
    props: ["video"],
    components: {
        Avatar,
        Replies,
    },
    mounted() {
        this.fetchComments();
    },
    computed: {
        auth() {
            return __auth();
        }
    },
    data: () => ({
        comments: {
            data: [],
        },
        newComment: "",
    }),
    methods: {
        fetchComments() {
            const url = this.comments.next_page_url ? this.comments.next_page_url : `/videos/${this.video.id}/comments`;
            axios.get(url).then(({ data }) => { // "data" is a reserved keyword in axios
                this.comments = {
                    ...data,
                    data: [
                        ...this.comments.data,
                        ...data.data
                        ],
                    };
            });
        },
        addComment() {
            if(!this.newComment) return;
            axios.post(`/comments/${this.video.id}/`, {
                body: this.newComment,
            }).then(({ data }) => {
                this.comments = {
                    ...this.comments,
                    data: [
                        data,
                        ...this.comments.data,
                    ],
                };
            });
        }
    }
}
</script>
