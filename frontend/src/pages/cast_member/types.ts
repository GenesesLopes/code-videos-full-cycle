import { AxiosResponse } from 'axios';

export interface CastMember {
    id: string;
    name: string;
    type: number;
}

export interface CastMemberResponse extends AxiosResponse {
    data: CastMember[]
}