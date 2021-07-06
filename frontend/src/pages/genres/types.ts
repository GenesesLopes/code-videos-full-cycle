import { AxiosResponse } from 'axios';
import { Category } from '../category/types';

interface Categories extends Category {
    pivot: {
        genre_id: string;
        category_id: string
    }
}

export interface Genres {
    id: string;
    name: string;
    is_active: boolean;
    categories: Categories[]
}

export interface GenresResponse extends AxiosResponse {
    data: Genres[]
}