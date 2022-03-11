<template>
    <div class="card mt-5 p-5">
        <div class="media d-flex" v-for="comment in comments.data">
            <div>
                <avatar :username="comment.user.name" :size="30" class="mr-3"></avatar>
            </div>
            <div class="media-body" style="margin-left: 10px;">
                <h6 class="mt-0">{{ comment.user.name }}</h6>
                <small>
                    {{ comment.body }}
                </small>
                <div class="from-inline my-4 w-full d-flex">
                    <input type="text" class="form-control from-control-sm w-80">
                    <button class="btn btn-sm btn-primary">
                        <small>Add comment</small>
                    </button>
                </div>
                <div class="media mt-3 d-flex">
                    <a class="mr-3" href="#">
                        <img width="30" height="30" class="rounded-circle mr-3" src="https://picsum.photos/id/42/200/200">
                    </a>
                    <div class="media-body" style="margin-left: 10px;">
                        <h6 class="mt-0">Media heading</h6>
                        <small>Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque anta sollicitudin. Cras putus odio, vestilbulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia comgue felis in faucibus.</small>
                        <div class="from-inline my-4 w-full d-flex">
                            <input type="text" class="form-control from-control-sm w-80">
                            <button class="btn btn-sm btn-primary">
                                <small>Add comment</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="text-center">
            <button class="btn btn-success">
                Load More
            </button>
        </div>
    </div>

</template>

<script>
import Avatar from "vue-avatar";
import axios from 'axios';
export default {
    props: ["video"],
    components: {
        "avatar": Avatar
    },
    mounted() {
        this.fetchComments();
    },
    data: () => ({
        comments: {
            data: [],
        }
    }),
    methods: {
        fetchComments() {
            axios.get(`/videos/${this.video.id}/comments`).then(({ data }) => {
                this.comments = data;
            });
        }
    }
}
</script>
