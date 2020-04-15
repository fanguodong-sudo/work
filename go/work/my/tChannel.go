package my

import (
	"fmt"
	"time"
)

type Tchannel struct {

}


func (this *Tchannel) Index(){


	this.mChannel()
	//this.forChannel();
}


//todo elastic 使用方法
func (this *Tchannel) mChannel(){

}

func (this *Tchannel) forChannel(){
	ch := make(chan int)
	count := 6
	go func() {
		for i:=1;i <= count;i++ {
			ch <- i
			time.Sleep(time.Second * 3)
		}
	}()

	for item := range ch{
		fmt.Println("work:",item)
		if item == count {
			break
		}
	}
}