import { AxiosResponse } from 'axios';

export interface Category {
    id: string;
    name: string;
}

export interface CategoryResponse extends AxiosResponse {
    data: Category[]
}