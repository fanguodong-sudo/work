package tListen

import (
	"../my"
	"net"
)

type Tclient struct {
	log *my.Tlog
}

func NewTclient() *Tclient {
	t := new(Tclient)
	t.log = new(my.Tlog)
	return t
}

func (this *Tclient) Index(){
	conn, err := net.Dial("tcp","127.0.0.1:8000")
	if(err != nil){
		this.log.Error.Println("err=",err)
		panic(err)
	}

	defer conn.Close()
	conn.Write([]byte("can you hear me"))
}