import axios from "axios";
import { GET_CLIENT_LIST_URL, UPDATE_COMPUTER_URL } from "../../utils/constants";

import '../../utils/modal';

import '../../utils/content-toggle';

import './components/client';

import './components/computer';

import './components/booking';

const getClients = async () => {
    await axios.post(
        GET_CLIENT_LIST_URL
    ).then(r => {
        console.log(r)
    })
}