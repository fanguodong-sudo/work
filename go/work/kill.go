package main

import (
	"fmt"
	"os"
)

func main (){

	item := os.Args

	for _,v := range item{
		fmt.Println(v)
	}


}
