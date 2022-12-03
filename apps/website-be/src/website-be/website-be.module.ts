import { TypeOrmModule } from '@nestjs/typeorm';
import { Module } from '@nestjs/common';

import { CategoryBeModule } from '../category-be/category-be.module';
import { KeywordEntity } from '../entities/keyword.entity';
import { KeywordBeModule } from '../keyword-be/keyword-be.module';

@Module({
  imports: [
    CategoryBeModule,
    KeywordBeModule,
    TypeOrmModule.forRoot({
      type: 'mysql',
      logging: true,
      host: 'localhost',
      port: 3306,
      username: 'root',
      password: 'root',
      database: 'profile-piler',
      entities: [KeywordEntity],
      synchronize: false,
    }),
  ],
})
export class WebsiteBeModule {}
