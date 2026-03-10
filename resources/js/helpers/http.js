import axios from 'axios';

const http = () => {
    let options = {
        headers: {}
    }

    if(localStorage.getItem('token')) {
        options.headers.Authorization = `Bearer ${localStorage.getItem('token')}`;
    }

    return axios.create(options);
}

export default http;
