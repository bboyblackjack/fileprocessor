### To test console command, you should: 
1. Run: ```docker compose up -d --build``` (or docker-compose, if you use older version)
2. Check docker containers: ```docker ps``` (not required)
3. Run: ```docker exec -it php-apache bash```
4. Run: ```./testapp/protected/yiic fileprocessor```

P.S. For simplicity, test files placed in console command folder.
