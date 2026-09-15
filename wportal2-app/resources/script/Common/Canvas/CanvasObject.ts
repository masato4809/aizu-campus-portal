export class CanvasObject {
  private readonly uid: string;

  constructor(uid: string) {
    this.uid = uid;
  }

  public getUid(): string {
    return this.uid;
  }
}
