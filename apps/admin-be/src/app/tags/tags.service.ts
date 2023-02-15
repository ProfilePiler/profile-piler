import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Tags } from './tags.entity';

@Injectable()
export class TagsService {

    constructor(
        @InjectRepository(Tags)
        private readonly tagRepo: Repository<Tags>
    ){}

    findAll(){
        return this.tagRepo.find()
    }
}