## Laravel Init project

### 安装必要组件
```shell
composer install
```

### 修改配置文件
```shell
cp ./.env.example ./.env
vim ./.env
```

### 更改 `database/migrations/` 相关数据库迁移文件

### 运行数据库迁移
```shell
php artisan migrate
```

### 上传图片连接
```shell
php artisan storage:link
```

### 生成 Passport Key
```shell
php artisan passport:key
```

### 生成 Passport 客户端
```shell
php artisan passport:client --personal

#1
What should we name the personal access client? []:
> User
Personal access client created successfully.
Client ID: 1
Client secret: 7KqVA8gPxRhPtFdfdsfsuVi4n3xpUBOEiNW4lPI

#2

php artisan passport:client --personal
What should we name the personal access client? []:
> Admin
Personal access client created successfully.
Client ID: 2
Client secret: 7KqVA8gPxRhPtFdfdsfsuVi4n3xpUBOEiNW4fde
```