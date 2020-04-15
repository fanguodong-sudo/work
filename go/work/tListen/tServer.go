package tListen

import (
	"../my"
	"fmt"
	"net"
)

type Tserver struct {
	log *my.Tlog
}

func NewTserver() *Tserver {
	t := new(Tserver)
	t.log = new(my.Tlog).Init()
	return t
}

func (this *Tserver) Index(){
	listener,err := net.Listen("tcp","127.0.0.1:8000")
	if(err != nil){
		this.log.Error.Println("侦听失败,err = ",err)
		panic(err)
	}

	defer listener.Close()

	//阻塞等待用户链接
	conn,err := listener.Accept()
	if err != nil {
		this.log.Error.Println("err = ",err)
		panic(err)
	}

	buff := make([]byte,1024) //缓存区
	n,err1 := conn.Read(buff)

	if err1 != nil {
		this.log.Error.Println("err = ",err)
		panic(err)
	}

	fmt.Println("buf = ", string(buff[:n]))



	this.log.Info.Println("done")
	defer conn.Close() //关闭当前用户链接


}