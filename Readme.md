# Microservice Task

Proje içinde 5 adet servis bulunmaktadır, çoğu `HTTP` ile haberleşiyorken, bazıları `RabbitMQ` ile haberleşiyor.

## Beklentiler
- [x] Micro servis mimarisinde olmalıdır.
- [x] Her servisin kendi database’i olmalıdır.
- [x] Servisler kendi arasında Request atarak yada bir servis bus(rabbitmq yada kafka) vasıtasıyla konuşmalıdır.
- [x] Test caseler yazılmalıdır.
- [x] Sistem docker üzerinde çalışmalıdır.
- [x] Laravel 10 yada daha yeni sürüm kullanılmalıdır.

## Servislerin Tanımı
### api-gateway
api-gateway servisi, diğer servislere gitmek isteyen kullanıcıları yönlendirir ve burdan oturumlarını kontrol eder. Oturumu geçerli olan kullanıcı, diğer servislere erişebilir hale geliyor. Normal şartlarda dışarıya açık olması gereken tek servistir.

### security
security servisi, kullanıcı yönetimiyle ilgilenir ve kullanıcının oturumunu doğrular. Kullanıcının diğer servisleri kullanabilmesi için bu servisi kullanmalıdır.

- [x] Sadece user modeli vardır
- [x] User create hariç tüm endpointlere token ile gidilmelidir
- [x] User oluşturulduktan sonra hoşgeldin maili gönderilmelidir (içerik önemsiz)
- [x] User oluşturulduktan sonra kullanıcıya lisans atanmalıdır. (30 günlük demo lisans) (mümkünse rabbitmq üzerinden oluşturulmalıdır.)

### license
license servisi, kullanıcının lisansıyla ilgilenir, lisansın süresi dolarsa kullanıcıyı bilgilendirir ve diğer servislere kullanıcılarla ilgili kısıtlamalar uygulanması için bilgi sağlar.
- [x] Her kullanıcının bir aktif lisansı olabilir
- [x] Lisans demo olabilir
- [x] Demo lisansı bir kez atanabilir
- [x] Demo lisans 30 gün kullanılabilir
- [x] Lisans maximum dosya boyutu bilgisi tutar (default 100 mb)
- [x] Lisans günlük gönderilecek dosya sayısını tutar (default 5)
- [x] Lisans kota bilgisi tutar

### file-management
file-management servisi, kullanıcıların dosyalarıyla ilgilenir. file-management servisi, `license` servisini de kullanır. Bunu kullanmasının sebebi; kullanıcının kısıtlamalarına göre hareket etmesini sağlamaktır.

- [x] Dosya oluşturma ve transferinden sorumludur.
- [x] Dosya S3, Minio gibi bir ortamda tutulabilir. Ancak Storage altında da olabilir. (Optional)
- [x] File Create ederken upload yapılabilir.
- [x] File Download edilmelidir.
- [x] File Create Yada Upload aşamasında lisans kontrolü yapılmalıdır.
- [x] Lisans üzerindeki kısıtlamalara uygun davranılmalıdır.
- [x] Dosya yüklendiğinde yada silindiğinde bildirim gönderilmelidir.
- [x] Kota aşım durumlarında da bildirim gönderilmelidir.

### notification
notification servisi, kullanıcının bildirimleriyle ilgilenir. Diğer servislerden yapılan bildirimleri, kullanıcıya bildirmek üzere toplar.

- [ ] Gönderilen notificationlar görüntülenebilmelidir (Horizon)
- [x] Bu serviste bir endpoint yoktur
- [x] Haberleşme rabbitmq üzerinden yapılmalıdır