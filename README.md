# ASRank API V2 Project

Install INFLUXDB on Linux
--------------------------------------
Update system:
sudo apt-get update
sudo curl -sL https://repos.influxdata.com/influxdb.key | sudo apt-key add -

Linux Ubuntu (16)
sudo echo "deb https://repos.influxdata.com/ubuntu trusty stable" | sudo tee /etc/apt/sources.list.d/influxdb.list

Linux Ubuntu (18)
sudo echo "deb https://repos.influxdata.com/ubuntu bionic stable" | sudo tee /etc/apt/sources.list.d/influxdb.list

Linux Mint (18.3)
sudo echo "deb https://repos.influxdata.com/ubuntu/dists/xenial/ stable" | sudo tee -a /etc/apt/sources.list

Reupdate
sudo apt-get update

Install Influx
sudo apt-get -y install influxdb

Remove Fluxdb
sudo apt-get remove influxdb

Activate as service
sudo systemctl start influxdb
sudo  systemctl enable influxdb
sudo systemctl status influxdb

Influxdb connection
Connected to http://localhost:8086 version 1.6.1

Influxdb Http Query
http://localhost:8086/query?pretty=true&db=asrank&q=SELECT COUNT(time) FROM locations LIMIT 3

Influx CLI
influx

Install REDIS on Linux
--------------------------------------

1.Update system:
  sudo apt-get update
  sudo apt-get upgrade

2.Install Redis Server with apt or apt-get:
  sudo apt (apt-get) install redis-server

3.Set autoload Redis as linux daemon:
  sudo systemctl enable redis-server.service

  disable autostart mode:
  sudo systemctl disable redis

4.Проверка, что сервис запущен:
  systemctl status redis
  
5.Start command console for Redis Server
  redis-cli

6.Test response from Redis:
  ping
  
  Response:
  PONG

7.Install php extention for Redis:
  sudo apt-get install php-redis