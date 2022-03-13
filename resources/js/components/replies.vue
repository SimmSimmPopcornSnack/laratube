<template>
    <div>
        <div class="from-inline my-4 w-full d-flex">
            <input type="text" class="form-control from-control-sm w-80">
            <button class="btn btn-sm btn-primary">
                <small>Add comment</small>
            </button>
        </div>
        <div class="media mt-3 d-flex" v-for="reply in replies.data">
            <a class="mr-3" href="#">
                <!-- <img width="30" height="30" class="rounded-circle mr-3" src="https://picsum.photos/id/42/200/200"> -->
                <avatar :username="reply.user.name" :size="30" class="mr-3"></avatar>
            </a>
            <div class="media-body" style="margin-left: 10px;">
                <h6 class="mt-0">{{ reply.user.name }}</h6>
                <small>{{ reply.body }}</small>
            </div>
        </div>

        <div v-if="comment.repliesCount > 0 && replies.next_page_url" class="text-center">
            <button @click="fetchReplies" class="btn btn-info btn-sm">Load Replies</button>
        </div>
    </div>
</template>

<script>
import Avatar from "vue-avatar";

export default {
    props: ["comment"],
    components: {
        Avatar
    },
    data() {
        return {
            replies: {
                data: [],
                next_page_url: `/comments/${this.comment.id}/replies`,
            }
        }
    },
    methods: {
        fetchReplies() {
            axios.get(this.replies.next_page_url).then(({ data }) => { // "data" is a reserved keyword in axios
                this.replies = {
                    ...data,
                    data: [
                        ...this.replies.data,
                        ...data.data
                        ],
                    };
            });
        }
    }
}
</script>
