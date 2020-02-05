package my

import (
	"context"
	"fmt"
	"github.com/olivere/elastic"
	"log"
	"os"
)

type Telastic struct {

}


func(this *Telastic) Index(){

	eClient := this.init();

	eClient.Index()


}

func(this *Telastic) init() *elastic.Client{

	host := new(config).elasticUrl
	errorLog := log.New(os.Stdout, "APP", log.LstdFlags)
	elastic.SetSniff(false)
	client, err := elastic.NewClient(elastic.SetErrorLog(errorLog),elastic.SetURL(host))
	if err != nil {
		fmt.Println("elastic 连接失败 !")
		panic(err)
	}

	info,code,err := client.Ping(host).Do(context.Background())
	if err != nil {
		panic(err)
	}
	fmt.Printf("Elasticsearch returned with code %d and version %s\n", code, info.Version.Number)

	version,err := client.ElasticsearchVersion(host)
	if err != nil{
		panic(err)
	}

	fmt.Printf("Elasticsearch version is ",version)
	return client
}
