package main

import (
	"./my"
	"./tListen"
	"os"
)

var (
	myLog *my.Tlog
)
func init(){
	myLog = new(my.Tlog).Init()
}

func main(){

	action := os.Args[1]
	switch action {
		case "test":
			break
		case "t_mq":
			my.NewRmq().Index()
			break
		case "t_channel":
			new(my.Tchannel).Index()
			break
		case "t_elastic":
			new(my.Telastic).Index()
			break
		case "t_command":
			new(my.Tcommand).Index()
			break
		case "t_mysql":
			my.NewTmysql().Index()
			break
		case "t_panic":
			new(my.Tpanic).Index()
			break
		case "t_server":
			tListen.NewTserver().Index()
			break
		case "t_client":
			tListen.NewTclient().Index()
			break
		default:
			//tListen.NewTclient().Index()
			//fmt.Println("ffxxfdfdfdf")
			break
	}

}